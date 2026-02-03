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

/**
 * Weltspiegel Content Plugin
 *
 * Handles custom placeholders like {ytvideo VIDEO_ID}
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
     * Plugin that processes {ytvideo} placeholders
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

        // Find all {ytvideo VIDEO_ID} placeholders
        $pattern = '/{ytvideo\s+([a-zA-Z0-9_-]{11})}/i';

        if (preg_match_all($pattern, $row->text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $fullMatch = $match[0];
                $videoId = $match[1];

                // Generate replacement HTML using layout
                $replacement = $this->generateYouTubeEmbed($videoId);

                // Replace in content
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
     * Generate consent-aware YouTube embed HTML using layout
     *
     * @param   string  $videoId  YouTube video ID
     *
     * @return  string  HTML for the embed
     *
     * @since   0.1.0
     */
    private function generateYouTubeEmbed(string $videoId): string
    {
        return LayoutHelper::render('com_weltspiegel.youtube.embed', [
            'videoId' => $videoId,
            'responsive' => (bool) $this->params->get('yt_responsive', 1),
            'placeholder' => $this->params->get('yt_placeholder_text', 'YouTube-Trailer verfügbar'),
            'hint' => $this->params->get('yt_placeholder_hint', 'Bitte aktiviere YouTube in den Cookie-Einstellungen'),
        ]);
    }
}
