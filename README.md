# Panth Product Gallery

[![Magento 2.4.4 - 2.4.8](https://img.shields.io/badge/Magento-2.4.4%20--%202.4.8-orange)]()
[![PHP 8.1 - 8.4](https://img.shields.io/badge/PHP-8.1%20--%208.4-blue)]()
[![License: Proprietary](https://img.shields.io/badge/License-Proprietary-red)]()

**Custom product image gallery** for Magento 2 product detail pages.
Replaces the default Magento/Hyva gallery with a feature-rich,
configurable gallery that includes thumbnail layouts, image zoom,
fullscreen lightbox, and responsive touch navigation.

Works with both **Hyva** and **Luma** themes out of the box.

---

## Features

- **Three thumbnail layouts** — Horizontal (below), Vertical (left),
  and Grid (all visible)
- **Inner zoom on hover** — configurable zoom level (2x - 5x)
- **Fullscreen lightbox** — with mouse-wheel zoom, keyboard navigation,
  and image counter
- **Touch swipe** — mobile-friendly swipe navigation
- **Navigation arrows** — prev/next with optional infinite loop
- **SEO-friendly** — proper alt/title attributes on all images; optional
  integration with Panth_AdvancedSEO for template-based image alt text
- **Widget support** — embed the gallery on any CMS page or block
- **Admin configuration** — all settings configurable from
  `Stores > Configuration > Panth Extensions > Product Gallery`
- **Theme-aware** — auto-detects Hyva (Alpine.js) or Luma (vanilla JS)
  and renders the appropriate template

---

## Installation

### Composer (recommended)

```bash
composer require mage2kishan/module-productgallery
bin/magento module:enable Panth_ProductGallery
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

### Manual zip

1. Download the extension package zip
2. Extract to `app/code/Panth/ProductGallery`
3. Run the same `module:enable ... cache:flush` commands above

---

## Requirements

| | Required |
|---|---|
| Magento | 2.4.4 - 2.4.8 (Open Source / Commerce / Cloud) |
| PHP | 8.1 / 8.2 / 8.3 / 8.4 |
| Panth_Core | ^1.0 (installed automatically via Composer) |

---

## Configuration

Open **Stores > Configuration > Panth Extensions > Product Gallery**.

| Group | Settings |
|---|---|
| **General** | Enable/disable the module |
| **Gallery Layout** | Thumbnail layout type (horizontal/vertical/grid) |
| **Zoom** | Enable zoom, zoom type, zoom level |
| **Lightbox** | Enable lightbox, image counter, keyboard navigation |
| **Navigation** | Show arrows, enable swipe, infinite loop |

---

## Support

| Channel | Contact |
|---|---|
| Email | kishansavaliyakb@gmail.com |
| Website | https://kishansavaliya.com |
| WhatsApp | +91 84012 70422 |

Free email support is provided on a best-effort basis.

---

## License

Proprietary - see `LICENSE.txt`. Distribution is restricted to the
Adobe Commerce Marketplace.

---

## About the developer

Built and maintained by **Kishan Savaliya** (Panth Infotech) -
https://kishansavaliya.com. Builds high-quality, security-focused
Magento 2 extensions and themes for both Hyva and Luma storefronts.
