# Changelog

All notable changes to this extension are documented here. The format
is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [2.0.0] - Initial Marketplace release

### Added
- **Three thumbnail layouts** - Horizontal (below main image),
  Vertical (left of main image), and Grid (all images visible).
- **Inner zoom on hover** - configurable zoom level (2x - 5x) with
  large image preloading on first hover for best quality.
- **Fullscreen lightbox** - with mouse-wheel zoom, zoom in/out
  controls, keyboard navigation (arrow keys, Escape, +/-), and
  image counter.
- **Touch swipe navigation** - mobile-friendly swipe gestures on
  the main image area.
- **Navigation arrows** - prev/next arrows with optional infinite
  loop behavior.
- **SEO-friendly image attributes** - proper alt/title on all images;
  optional integration with Panth_AdvancedSEO for template-based
  image alt text with per-image position suffixes.
- **Widget support** - embed the gallery on any CMS page or block
  via the Magento widget system.
- **Admin configuration** - all settings configurable per store view
  from Stores > Configuration > Panth Extensions > Product Gallery.
- **Theme-aware rendering** - auto-detects Hyva (Alpine.js template)
  or Luma (vanilla JS template) and renders the appropriate version.
- **Plugin-based gallery replacement** - replaces the default
  Magento/Hyva gallery block output via an afterToHtml plugin, so
  no layout XML overrides are needed.
- **Observer for collection media** - ensures product collections
  include media gallery data when the module is enabled.
- **Unit tests** - PHPUnit tests for Block/Gallery, Helper/Data,
  and Observer/ProductCollectionLoadAfter.

### Compatibility
- Magento Open Source / Commerce / Cloud 2.4.4 - 2.4.8
- PHP 8.1, 8.2, 8.3, 8.4
- Hyva and Luma themes

---

## Support

For all questions, bug reports, or feature requests:

- **Email:** kishansavaliyakb@gmail.com
- **Website:** https://kishansavaliya.com
- **WhatsApp:** +91 84012 70422
