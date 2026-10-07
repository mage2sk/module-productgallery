# Panth Product Gallery - User Guide

Panth_ProductGallery replaces the default Magento product image gallery
with a custom-styled, feature-rich gallery. It supports configurable
thumbnail layouts, image zoom, fullscreen lightbox, and responsive
touch navigation. Compatible with both Hyva and Luma themes.

---

## Table of contents

1. [Installation](#1-installation)
2. [Verifying the module is active](#2-verifying-the-module-is-active)
3. [Configuration](#3-configuration)
4. [Gallery layouts](#4-gallery-layouts)
5. [Zoom settings](#5-zoom-settings)
6. [Lightbox settings](#6-lightbox-settings)
7. [Navigation settings](#7-navigation-settings)
8. [Widget usage](#8-widget-usage)
9. [Theme customization](#9-theme-customization)
10. [Troubleshooting](#10-troubleshooting)

---

## 1. Installation

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

## 2. Verifying the module is active

```bash
bin/magento module:status Panth_ProductGallery
# Module is enabled
```

Navigate to any product detail page on the storefront. If the module
is enabled, you should see the Panth gallery replacing the default
Magento gallery.

---

## 3. Configuration

Navigate to **Stores > Configuration > Panth Extensions > Product Gallery**.

All settings are store-view scoped, so you can configure different
gallery behavior per store view.

| Setting | Default | Description |
|---|---|---|
| **Enable Module** | Yes | Master on/off switch for the gallery |

---

## 4. Gallery layouts

Under **Gallery Layout**:

| Layout | Description |
|---|---|
| **Horizontal** | Thumbnails displayed below the main image in a scrollable row |
| **Vertical** | Thumbnails displayed to the left of the main image in a scrollable column |
| **Grid** | All images displayed in a responsive grid layout |

The active thumbnail is highlighted with a colored border matching
your theme's primary color.

---

## 5. Zoom settings

Under **Zoom**:

| Setting | Default | Description |
|---|---|---|
| **Enable Zoom** | Yes | Enable inner zoom on hover over the main image |
| **Zoom Type** | Inner | Inner zoom (image scales in place) or Lens (magnifying glass) |
| **Zoom Level** | 3 | Magnification level (2x to 5x) |

When zoom is enabled, hovering over the main image will display
a magnified view. The large (original) image is loaded on first
hover for best quality.

---

## 6. Lightbox settings

Under **Lightbox**:

| Setting | Default | Description |
|---|---|---|
| **Enable Lightbox** | Yes | Open fullscreen lightbox when clicking the main image |
| **Show Image Counter** | Yes | Display "1 / 5" counter in the lightbox |
| **Keyboard Navigation** | Yes | Arrow keys to navigate, Escape to close, +/- to zoom |

The lightbox supports mouse-wheel zoom (scroll up to zoom in, scroll
down to zoom out) and click-on-backdrop to close.

---

## 7. Navigation settings

Under **Navigation**:

| Setting | Default | Description |
|---|---|---|
| **Show Navigation Arrows** | Yes | Display prev/next arrows on the main image |
| **Enable Swipe** | Yes | Touch swipe navigation on mobile devices |
| **Infinite Loop** | No | Loop back to first image after reaching the last |

---

## 8. Widget usage

The gallery is also available as a Magento widget. To embed it on
a CMS page or block:

1. Go to **Content > Pages** (or **Blocks**)
2. Edit the page/block
3. Click **Insert Widget**
4. Select **Panth Product Gallery** from the widget type dropdown
5. Choose the template and save

The widget uses the same admin configuration settings as the
product detail page gallery.

---

## 9. Theme customization

Gallery colors and styling can be customized through the theme
configuration file at:

```
app/code/Panth/ProductGallery/etc/theme-config.json
```

Available CSS variables include:
- `gallery-primary` - Primary accent color
- `gallery-thumb-active-border` - Active thumbnail border color
- `gallery-lightbox-bg` - Lightbox backdrop color
- `gallery-border-radius` - Border radius for gallery elements
- `gallery-nav-bg` - Navigation arrow background color

---

## 10. Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| Default gallery still showing | Module disabled or cache not flushed | Check `Stores > Configuration > Panth Extensions > Product Gallery > Enable Module` is set to Yes; run `bin/magento cache:flush` |
| Gallery shows no images | Product has no media gallery images | Upload images to the product in the admin |
| Zoom not working | Zoom disabled in config | Check `Zoom > Enable Zoom` setting |
| Lightbox not opening | Lightbox disabled in config | Check `Lightbox > Enable Lightbox` setting |
| Gallery not styled correctly | Static content not deployed | Run `bin/magento setup:static-content:deploy -f` |

---

## Support

For all questions, bug reports, or feature requests:

- **Email:** kishansavaliyakb@gmail.com
- **Website:** https://kishansavaliya.com
- **WhatsApp:** +91 84012 70422

Free email support is provided on a best-effort basis.
