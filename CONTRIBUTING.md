# Contributing to Weltspiegel Content Plugin

## Development Setup

### Prerequisites

- Node.js LTS
- npm
- Local Joomla 6 installation for testing

### Setup

```bash
git clone https://github.com/weltspiegel-cottbus/plg_content_weltspiegel.git
cd plg_content_weltspiegel
npm install
```

## Making Changes

1. Create a feature branch: `git checkout -b feature/your-feature`
2. Make your changes
3. Test on a local Joomla installation
4. Build and verify: `npm run release`
5. Commit your changes

## Code Style

- Follow PSR-12 coding standards for PHP
- Use Joomla namespaced classes and modern PHP 8.5 features
- Add PHPDoc comments for all methods
- Use type hints and return types

## Testing

Before submitting changes:

1. Install on a clean Joomla 6 installation
2. Verify plugin enables without errors
3. Test YouTube embed placeholder parsing
4. Test cookie consent integration
5. Test attribs preservation when editing articles via standard editor

## Creating a Release

### Build Package Only

To create a ZIP package without changing the version:

```bash
npm run release
```

### Version Releases

1. Update version manually in:
   - `weltspiegel.xml`
   - `update-manifest.xml`

2. Run the appropriate release command:

   **Patch release** (bug fixes: 1.0.0 → 1.0.1):
   ```bash
   npm run release:patch
   ```

   **Minor release** (new features, backwards compatible: 1.0.0 → 1.1.0):
   ```bash
   npm run release:minor
   ```

   **Major release** (breaking changes: 1.0.0 → 2.0.0):
   ```bash
   npm run release:major
   ```

3. These commands will:
   - Bump version in `package.json`
   - Generate/update `CHANGELOG.md`
   - Create git commit and tag
   - Push to repository
   - Create packaged ZIP file

4. After release:
   - Go to GitHub Releases
   - Create a new release from the pushed tag
   - Upload the generated ZIP file
   - Write release notes based on CHANGELOG.md

## Project Structure

```
plg_content_weltspiegel/
├── .build/
│   └── package.mjs          # Build script
├── .github/
│   └── workflows/
│       └── release.yml      # GitHub Actions workflow
├── language/
│   ├── de-DE/
│   │   └── plg_content_weltspiegel.ini
│   └── en-GB/
│       └── plg_content_weltspiegel.ini
├── weltspiegel.php          # Main plugin file
├── weltspiegel.xml          # Plugin manifest
├── update-manifest.xml      # Update server manifest
├── package.json             # NPM configuration
├── CHANGELOG.md             # Version history
├── CONTRIBUTING.md          # This file
└── README.md                # Usage documentation
```

## Pull Requests

1. Fork the repository on GitHub
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes following the code style
4. Test thoroughly on a local Joomla installation
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request with a clear description
