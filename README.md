# Weltspiegel Content Plugin

A Joomla 5+ content plugin for the Weltspiegel Cottbus cinema website that processes custom placeholders for embedding rich media content.

## Features

- **YouTube Embed Placeholder**: Use `{ytvideo VIDEO_ID}` in articles to embed consent-aware YouTube videos
- **Component Integration**: Renders YouTube embeds using the `com_weltspiegel` layout (which handles thumbnail caching)
- **Cookie Consent Integration**: Respects user privacy by showing thumbnails until YouTube cookies are accepted
- **Configurable Defaults**: Admin-configurable placeholder texts and responsive behavior
- **Modern Joomla 5+**: Built with Joomla event system and SubscriberInterface

## Requirements

- Joomla 6.0 or higher
- PHP 8.1 or higher
- Weltspiegel Component (`com_weltspiegel`) for YouTube embed layout

## Installation

### Via Joomla Administrator

1. Download the latest release ZIP from [GitHub Releases](https://github.com/weltspiegel-cottbus/plg_content_weltspiegel/releases)
2. Navigate to **System → Extensions → Install**
3. Upload the ZIP file
4. Enable the plugin at **System → Plugins**
5. Search for "Weltspiegel" and click to enable

### Via Update Server (Automatic Updates)

The plugin includes an update server for automatic updates:

1. Install the plugin as described above
2. The update server is automatically configured
3. Check for updates at **System → Update → Extensions**

## Configuration

After installation, configure the plugin at **System → Plugins → Content - Weltspiegel**:

### YouTube Settings

- **Placeholder Text**: Text shown when consent not granted (default: "YouTube-Trailer verfügbar")
- **Placeholder Hint**: Small text below placeholder (default: "Bitte aktiviere YouTube in den Cookie-Einstellungen")
- **Responsive Embed**: Use responsive 16:9 aspect ratio (default: Yes)

## Usage

### Embedding YouTube Videos

Add the following placeholder to any Joomla article:

```
{ytvideo VIDEO_ID}
```

Replace `VIDEO_ID` with the 11-character YouTube video ID (from the URL).

**Example:**

For the video `https://www.youtube.com/watch?v=dQw4w9WgXcQ`, use:

```
{ytvideo dQw4w9WgXcQ}
```

### How It Works

1. Plugin detects `{ytvideo VIDEO_ID}` placeholders during content rendering
2. Renders the `com_weltspiegel` YouTube embed layout
3. The component layout handles thumbnail caching and consent-aware display
4. When user accepts YouTube cookies, the iframe loads automatically
5. Direct link to YouTube is always available for users who decline cookies

## Development

### Prerequisites

- Node.js LTS
- npm

### Setup

```bash
# Clone the repository
git clone https://github.com/weltspiegel-cottbus/plg_content_weltspiegel.git
cd plg_content_weltspiegel

# Install dependencies
npm install
```

### Build & Package

```bash
# Build and create ZIP for installation
npm run release
```

The packaged ZIP file will be created in the root directory.

### Version Management

```bash
# Patch release (0.1.0 → 0.1.1)
npm run release:patch

# Minor release (0.1.0 → 0.2.0)
npm run release:minor

# Major release (0.1.0 → 1.0.0)
npm run release:major
```

These commands:
- Bump version in `package.json`
- Generate/update `CHANGELOG.md`
- Create git tag
- Push to repository

### Project Structure

```
plg_content_weltspiegel/
├── .build/
│   └── package.mjs          # Build script
├── .github/
│   └── workflows/
│       └── release.yml      # GitHub Actions workflow
├── language/
│   └── de-DE/
│       └── plg_content_weltspiegel.ini
├── weltspiegel.php          # Main plugin file
├── weltspiegel.xml          # Plugin manifest
├── update-manifest.xml      # Update server manifest
├── package.json             # NPM configuration
└── README.md
```

## Contributing

Contributions are welcome! Please follow these guidelines:

1. **Fork the repository** on GitHub
2. **Create a feature branch** (`git checkout -b feature/amazing-feature`)
3. **Make your changes** following the code style
4. **Test thoroughly** on a local Joomla installation
5. **Commit your changes** (`git commit -m 'Add amazing feature'`)
6. **Push to the branch** (`git push origin feature/amazing-feature`)
7. **Open a Pull Request**

### Code Style

- Follow PSR-12 coding standards for PHP
- Use Joomla 5 namespaced classes
- Add PHPDoc comments for all methods
- Use type hints and return types

### Testing

Before submitting a PR:

1. Test on a clean Joomla 6 installation
2. Verify plugin enables without errors
3. Test YouTube embed placeholder parsing
4. Test cookie consent integration

## Related Projects

- [tpl_weltspiegel](https://github.com/weltspiegel-cottbus/tpl_weltspiegel) - Weltspiegel Joomla template (required for layout overrides)
- [com_weltspiegel](https://github.com/weltspiegel-cottbus/com_weltspiegel) - Weltspiegel component for movie/event management

## License

MIT License - see [LICENSE](LICENSE) file for details.

Copyright (c) 2025 Weltspiegel Cottbus

## Maintainer

**Michael Buchholz**
[Weltspiegel Cottbus](https://www.weltspiegel-cottbus.de)

## Support

- **Issues**: [GitHub Issues](https://github.com/weltspiegel-cottbus/plg_content_weltspiegel/issues)
- **Releases**: [GitHub Releases](https://github.com/weltspiegel-cottbus/plg_content_weltspiegel/releases)
