<?php

/**
 * @package     Weltspiegel\Plugin\Content\Weltspiegel
 *
 * @copyright   Weltspiegel Cottbus
 * @license     MIT; see LICENSE file
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\Filesystem\Folder;

/**
 * Weltspiegel Content Plugin
 *
 * Handles custom placeholders like {ytvideo VIDEO_ID} and {gallery path}
 *
 * @since 0.1.0
 */
class PlgContentWeltspiegel extends CMSPlugin implements SubscriberInterface
{
    /**
     * Custom attribs fields to preserve when saving through standard editor
     *
     * @since 0.1.0
     */
    private const array PRESERVE_ATTRIBS = ['source', 'youtube_url', 'tagline'];

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return array
     *
     * @since 0.1.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onContentPrepare' => 'onContentPrepare',
            'onContentBeforeSave' => 'onContentBeforeSave',
        ];
    }

    /**
     * Allowed image extensions for gallery scanning
     *
     * @var    array
     * @since  1.1.0
     */
    private const array IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    /**
     * Plugin that processes {ytvideo} and {gallery} placeholders
     *
     * @param   Event  $event  The event object
     *
     * @return  void
     *
     * @since   0.1.0
     */
    public function onContentPrepare(Event $event)
    {
        // Extract arguments from event
        [$context, $row, $params, $page] = array_values($event->getArguments());

        // Check if we have content to process
        if (!isset($row->text) || empty($row->text)) {
            return;
        }

        // Process {ytvideo VIDEO_ID} placeholders
        $ytPattern = '/{ytvideo\s+([a-zA-Z0-9_-]{11})}/i';

        if (preg_match_all($ytPattern, $row->text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $fullMatch = $match[0];
                $videoId = $match[1];

                $replacement = $this->generateYouTubeEmbed($videoId);
                $row->text = str_replace($fullMatch, $replacement, $row->text);
            }
        }

        // Process {gallery folder/path} and {gallery folder/path|key=val|key=val} placeholders
        $galleryPattern = '/{gallery\s+([^}|]+?)(?:\|([^}]*?))?}/i';

        if (preg_match_all($galleryPattern, $row->text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $fullMatch = $match[0];
                $folder = trim($match[1]);
                $optionString = $match[2] ?? '';

                $replacement = $this->renderGallery($folder, $this->parseGalleryOptions($optionString));
                $row->text = str_replace($fullMatch, $replacement, $row->text);
            }
        }
    }

    /**
     * Preserve custom attribs when saving through standard Joomla article editor
     *
     * @param   Event  $event  The event object
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function onContentBeforeSave(Event $event): void
    {
        [$context, $article, $isNew] = array_values($event->getArguments());

        // Only process existing articles in com_content
        if ($context !== 'com_content.article' || $isNew || empty($article->id)) {
            return;
        }

        // Load current attribs from database
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true)
            ->select($db->quoteName('attribs'))
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('id') . ' = ' . (int) $article->id);
        $db->setQuery($query);
        $oldAttribsJson = $db->loadResult();

        if (empty($oldAttribsJson)) {
            return;
        }

        $oldAttribs = json_decode($oldAttribsJson, true) ?: [];
        $newAttribs = is_string($article->attribs)
            ? (json_decode($article->attribs, true) ?: [])
            : ($article->attribs ?: []);

        // Preserve custom fields from old attribs if not present in new
        foreach (self::PRESERVE_ATTRIBS as $field) {
            if (isset($oldAttribs[$field]) && !isset($newAttribs[$field])) {
                $newAttribs[$field] = $oldAttribs[$field];
            }
        }

        $article->attribs = json_encode($newAttribs);
    }

    /**
     * Generate YouTube embed HTML using component layout
     *
     * @param   string  $videoId  YouTube video ID
     *
     * @return  string  HTML for the embed
     *
     * @since   0.1.0
     */
    private function generateYouTubeEmbed(string $videoId): string
    {
        return LayoutHelper::render('com_weltspiegel.youtube.embed', ['videoId' => $videoId]);
    }

    /**
     * Parse gallery option string into associative array
     *
     * @param   string  $optionString  Pipe-separated key=value pairs (e.g. "cols=3|preview=hero.jpg")
     *
     * @return  array  Parsed options
     *
     * @since   1.1.0
     */
    private function parseGalleryOptions(string $optionString): array
    {
        $options = [];

        if ($optionString === '') {
            return $options;
        }

        foreach (explode('|', $optionString) as $pair) {
            $parts = explode('=', $pair, 2);
            if (count($parts) === 2) {
                $options[trim($parts[0])] = trim($parts[1]);
            }
        }

        return $options;
    }

    /**
     * Get sorted list of image files from a folder
     *
     * @param   string  $folderPath  Full filesystem path to the folder
     *
     * @return  array  Sorted list of image paths relative to site root
     *
     * @since   1.1.0
     */
    private function getGalleryImages(string $folderPath, string $relativePath): array
    {
        $filter = '\.(' . implode('|', self::IMAGE_EXTENSIONS) . ')$';
        $files = Folder::files($folderPath, $filter, false, false);
        sort($files);

        return array_map(
            fn(string $file): string => $relativePath . '/' . $file,
            $files
        );
    }

    /**
     * Render a gallery from a folder path and options
     *
     * @param   string  $folder   Folder path from the tag (e.g. "images/vorschauen/album")
     * @param   array   $options  Parsed options from the tag
     *
     * @return  string  Rendered gallery HTML or empty string on error
     *
     * @since   1.1.0
     */
    private function renderGallery(string $folder, array $options): string
    {
        // Validate and resolve folder path within JPATH_ROOT
        $fullPath = realpath(JPATH_ROOT . '/' . $folder);
        $imagesRoot = realpath(JPATH_ROOT . '/images');

        // Security: ensure path exists and is within /images/
        if ($fullPath === false || $imagesRoot === false || !str_starts_with($fullPath, $imagesRoot)) {
            return '';
        }

        if (!is_dir($fullPath)) {
            return '';
        }

        $images = $this->getGalleryImages($fullPath, $folder);

        if (empty($images)) {
            return '';
        }

        return LayoutHelper::render('com_weltspiegel.gallery.default', [
            'folder'  => $folder,
            'images'  => $images,
            'options' => $options,
        ]);
    }
}
