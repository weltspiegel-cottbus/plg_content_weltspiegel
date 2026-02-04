# Weltspiegel Content Plugin

A Joomla 6 content plugin for the Weltspiegel Cottbus cinema website.

## Features

### YouTube Embed Placeholder

Embed consent-aware YouTube videos in articles using a simple placeholder syntax:

```
{ytvideo VIDEO_ID}
```

**Example:** For `https://www.youtube.com/watch?v=dQw4w9WgXcQ`:
```
{ytvideo dQw4w9WgXcQ}
```

The plugin replaces the placeholder with the consent-aware YouTube embed from `com_weltspiegel`.

### Preserve Custom Article Attributes

When articles are created via `com_weltspiegel` (e.g., Vorschauen, Veranstaltungen), they store custom data in the `attribs` field:
- `source` - Marks the article as component-managed
- `youtube_url` - YouTube video ID for trailers
- `tagline` - Short tagline displayed below the title

If these articles are later edited via the standard Joomla article editor, these custom fields would normally be lost. This plugin preserves them automatically during save.

## Requirements

- Joomla 6.0+
- PHP 8.1+
- `com_weltspiegel` (for YouTube embed layout)

## Installation

1. Download the latest release ZIP
2. Navigate to **System → Extensions → Install**
3. Upload the ZIP file
4. Enable the plugin at **System → Plugins → Content - Weltspiegel**

## Building

To create an installable ZIP package:

```bash
npm install
npm run release
```

The packaged `plg_content_weltspiegel-x.x.x.zip` will be created in the root directory.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for development setup and release instructions.

## License

MIT License - see [LICENSE](LICENSE) for details.

## Links

- [Weltspiegel Cottbus](https://www.weltspiegel-cottbus.de)
- [Issues](https://github.com/weltspiegel-cottbus/plg_content_weltspiegel/issues)
- [Releases](https://github.com/weltspiegel-cottbus/plg_content_weltspiegel/releases)
