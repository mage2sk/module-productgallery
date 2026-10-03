# Magento 2 Product Gallery

Panth Product Gallery replaces the default image gallery on the Magento 2 product detail page with a gallery that is configured from the admin. It offers horizontal, vertical and grid thumbnail layouts, inner zoom on hover, a fullscreen lightbox with an image counter and keyboard navigation, and prev/next arrows on the main image. When the module is enabled, a plugin on `Magento\Catalog\Block\Product\View\Gallery` swaps the output of the standard gallery block for the module's own template; when it is disabled, Magento renders its default gallery again.

The module is aimed at merchants who want a different gallery layout on the product page without theme work. It ships two templates: one for Hyva (Alpine.js and Tailwind CSS) and one for Luma (vanilla JavaScript). The active theme is detected through `Panth_Core` and the matching template is selected automatically.

Product page: [kishansavaliya.com/magento-2-productgallery.html](https://kishansavaliya.com/magento-2-productgallery.html)

## Features

- Three thumbnail layouts selectable in the admin: "Horizontal Thumbnails (Below Main Image)", "Vertical Thumbnails (Left of Main Image)" and "Grid (All Images Visible)".
- Inner zoom on hover over the main image (3x by default); the Luma template preloads the large image on first hover, the Hyva template shows the large image in a positioned overlay.
- Fullscreen lightbox opened by clicking the main image (or a grid item on Hyva), with prev/next controls, an optional "1 / 5" style image counter, zoom in/out buttons and mouse-wheel zoom.
- Optional keyboard navigation in the lightbox: arrow keys to move between images, Escape to close, "+" and "-" to zoom (the Hyva template also resets zoom with "0").
- Optional prev/next arrows on the main image and an optional infinite loop from the last image back to the first.
- Optional touch swipe on the main image in both templates (a left or right swipe of more than 50 px changes the image).
- The gallery opens on the product's base image.
- On Hyva, the horizontal and vertical layouts follow configurable product option changes: the `update-gallery` event sent by the Hyva configurable options script replaces the images and thumbnails, and `reset-gallery` restores the parent product images.
- On Luma, the gallery follows configurable swatch and drop-down option changes. The gallery element carries `data-gallery-role="gallery-placeholder"` and exposes a small gallery API through jQuery `data('gallery')` (`updateData`, `returnCurrentImages`, `first`, `last`, `prev`, `next`, `seek`), which Magento's `swatch-renderer.js` and `configurable.js` call instead of the Fotorama gallery. The main image, thumbnails and arrows are rebuilt from the images of the selected child product, and resetting the options restores the parent images. Product videos are not shown by this gallery; when it replaces the Luma gallery, the core product video initialisation block (`product.info.media.video`) is not rendered.
- Disabled gallery entries are skipped and images are ordered by their media gallery position.
- Every image gets an `alt` attribute and a `title` attribute, taken from the caption and title of the core gallery images JSON (see Optional alt text integration below).
- Media gallery data is added to product collections while the module is enabled, so the images are available without a second load.
- A "Panth Product Gallery" widget is declared in `etc/widget.xml` for use through the Magento widget system.
- Admin settings are available at default, website and store view scope.
- Unit tests are included under `Test/Unit`.

## Compatibility

| Platform | Versions |
|---|---|
| Magento Open Source | 2.4.4 to 2.4.8 |
| Adobe Commerce | 2.4.4 to 2.4.8 |
| PHP | >= 8.1 (from `composer.json`) |
| Themes | Hyva (Alpine.js template) and Luma (vanilla JavaScript template) |

Composer constraints for Magento packages: `magento/framework ^103.0`, `magento/module-catalog ^104.0`, `magento/module-store ^101.0`, `magento/module-backend ^102.0`, `magento/module-widget ^101.0`, `magento/module-config ^101.0`.

## Requirements

- Magento Open Source or Adobe Commerce 2.4.4 to 2.4.8
- PHP 8.1 or newer
- `mage2kishan/module-core` `^1.0` (required; provides the "Panth Extensions" configuration tab, the admin menu parent, the theme detection helper and the theme-config view model)
- Suggested: `hyva-themes/magento2-default-theme` `^1.0` when the store runs on Hyva

## Installation

```bash
composer require mage2kishan/module-productgallery
bin/magento module:enable Panth_Core Panth_ProductGallery
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

`setup:di:compile` is only needed in production mode. `setup:static-content:deploy -f` is listed because the module ships a LESS file under `view/frontend/web/css/source/` for Luma.

Check that the module is enabled:

```bash
bin/magento module:status Panth_ProductGallery
```

## Configuration

Go to Stores > Configuration > Panth Extensions > Product Gallery. The section is also reachable from the admin menu under Panth Extensions > Product Gallery > Configuration. All settings can be set at default, website and store view scope.

### General Settings

| Setting | Default | What it does |
|---|---|---|
| Enable Module | Yes | Enables the gallery replacement. When set to No, the plugin and the collection observer do nothing and Magento's default gallery is shown. |

### Gallery Layout

| Setting | Default | What it does |
|---|---|---|
| Thumbnail Layout | Horizontal Thumbnails (Below Main Image) | Chooses how thumbnails are displayed relative to the main image: horizontal strip below, vertical column on the left, or a grid with all images visible. |

### Zoom

| Setting | Default | What it does |
|---|---|---|
| Enable Zoom | Yes | Enables inner zoom on hover over the main gallery image. |

### Lightbox

| Setting | Default | What it does |
|---|---|---|
| Enable Lightbox | Yes | Opens a fullscreen lightbox when the main image is clicked. |
| Show Image Counter | Yes | Shows a "1 / 5" counter in the lightbox. Only shown when "Enable Lightbox" is Yes. |
| Keyboard Navigation | Yes | Enables arrow key and Escape key handling in the lightbox. Only shown when "Enable Lightbox" is Yes. |

### Navigation

| Setting | Default | What it does |
|---|---|---|
| Show Navigation Arrows | Yes | Shows prev/next arrows on the main image when the product has more than one image. |
| Enable Swipe | Yes | Enables touch swipe navigation on the main image. |
| Infinite Loop | No | When Yes, prev/next wrap around from the last image to the first and back. |

### Design & Colors

This group has no fields. It explains that gallery colours and sizes come from `etc/theme-config.json`, which is registered with the `Panth\Core\ViewModel\ThemeConfig` view model from `Panth_Core`. The file defines CSS variables such as `--gallery-primary`, `--gallery-thumb-active-border`, `--gallery-zoom-bg`, `--gallery-lightbox-bg` and `--gallery-border-radius`, which are used by the Luma LESS file.

Config paths:

- `panth_productgallery/general/enabled`
- `panth_productgallery/layout/layout_type`
- `panth_productgallery/zoom/enable_zoom`
- `panth_productgallery/lightbox/enable_lightbox`
- `panth_productgallery/lightbox/show_counter`
- `panth_productgallery/lightbox/enable_keyboard_nav`
- `panth_productgallery/navigation/show_arrows`
- `panth_productgallery/navigation/enable_swipe`
- `panth_productgallery/navigation/infinite_loop`

`etc/config.xml` also sets defaults that have no admin field and are read through `Helper\Data`: `layout/thumb_position` (bottom), `layout/main_image_width` and `layout/main_image_height` (700), `layout/thumb_width` and `layout/thumb_height` (72), `layout/visible_thumbs` (5), `zoom/zoom_type` (inner) and `zoom/zoom_level` (3, clamped to 2 to 5). The main image and thumbnail sizes, and the thumbnail `width` and `height` attributes, are taken from these values.

Default behaviour after installation: the module is enabled, the horizontal layout is used, zoom, lightbox, counter, keyboard navigation and arrows are on, and infinite loop is off.

## Usage

On the product page, `Plugin\HideDefaultGallery` runs as an `afterToHtml` plugin on `Magento\Catalog\Block\Product\View\Gallery`. It acts only on the blocks named `product.media` (Hyva) and `product.info.media.image` (Luma), builds the image list for the current product, and re-renders the block with the module template. If the product has no enabled images, or rendering fails, the original gallery HTML is returned. No layout XML overrides are needed; the layout files shipped with the module are intentionally empty.

- Hyva: `view/frontend/templates/hyva/gallery.phtml` is used. It is an Alpine.js component (`panthGallery()`) with Tailwind utility classes and inline styles, plus a lightbox script that appends its overlay to `document.body` and exposes it as `window.panthLightbox`.
- Luma: `view/frontend/templates/gallery.phtml` is used. It is plain JavaScript and includes its own inline `<style>` block; RequireJS and jQuery are only used, when present, to register the gallery API with `data('gallery')` and trigger `gallery:loaded`. Additional Luma styles are in `view/frontend/web/css/source/_module.less`.

Layouts: the horizontal and vertical layouts show a main image with a strip of clickable thumbnails (below or on the left) and, optionally, arrows; thumbnails are only rendered when the product has more than one image. On Hyva the grid layout lists all images in a 2-column grid (3 columns from the `md` breakpoint) without a separate main image, and clicking a grid item opens the lightbox. On Luma the grid option adds the `panth-gallery--grid` class to the wrapper, but the markup is the same main image with a thumbnail strip.

Zoom: while the cursor moves over the main image, the large image is shown scaled by the zoom level (3 by default) with the origin following the cursor. There is no lens or overlay zoom mode in the templates.

Lightbox: shows the large image size (`product_page_image_large`), the current position counter, prev/next arrows, zoom in/out buttons and a close button; clicking the backdrop closes it. On touch screens the image can be swiped left or right. The lightbox is a labelled dialog: focus moves to its close button, Tab stays inside it and focus returns to the main image on close; the main image itself opens the lightbox with Enter or Space. Keyboard handling is a `keydown` listener on the document that is only emitted when "Keyboard Navigation" is Yes. The lightbox always wraps around at the ends regardless of the "Infinite Loop" setting, which only affects the main image navigation.

Templates can be overridden in a theme in the usual way by placing a copy under `Panth_ProductGallery/templates/gallery.phtml` or `Panth_ProductGallery/templates/hyva/gallery.phtml`. Both templates read their data from the block: `panth_gallery_images`, `panth_gallery_config` and `panth_gallery_viewmodel` (an instance of `Panth\ProductGallery\ViewModel\Config`), which the plugin sets before rendering.

Widget: `etc/widget.xml` declares the "Panth Product Gallery" widget backed by `Block\Widget\Gallery`, with a single "Template" parameter ("Default Gallery"). The block renders the gallery for the product currently in the catalog registry and outputs nothing when no product is loaded, so it is only useful on pages where a product is set.

Optional alt text integration: the alt and title of each image are read from `getGalleryImagesJson()` of the core `Magento\Catalog\Block\Product\View\Gallery` block (the `caption` and, when present, `title` keys). `Plugin\HideDefaultGallery` uses the gallery block it wraps; `Block\Gallery` creates a core gallery block for the current product. Any module that rewrites that JSON, for example an image SEO module with an after-plugin on `getGalleryImagesJson()`, therefore also changes the alt and title in this gallery. Without such a module the caption is the image label, or the product name when no label is set. If the JSON cannot be read or does not match the product's media gallery, the image label or product name is used.

## Developer Notes

- Module name: `Panth_ProductGallery`
- Composer package: `mage2kishan/module-productgallery`
- Namespace: `Panth\ProductGallery` (PSR-4 from the module root)
- Load sequence: after `Magento_Catalog`, `Magento_Store`, `Panth_Core`
- Key classes:
  - `Plugin\HideDefaultGallery`: `afterToHtml` plugin on `Magento\Catalog\Block\Product\View\Gallery` (declared in `etc/frontend/di.xml`, sort order 10)
  - `Block\Gallery`: `AbstractProduct` block with `getGalleryImages()`, `getGalleryConfig()`, `getGalleryConfigJson()`, `isEnabled()`; `getTemplate()` picks the Hyva or Luma template
  - `Block\Widget\Gallery`: widget block extending `Block\Gallery`
  - `ViewModel\Config`: view model exposing all configuration getters and `getGalleryConfig()`
  - `Helper\Data`: configuration reader for `panth_productgallery/*`
  - `Observer\ProductCollectionLoadAfter`: observer on `catalog_product_collection_load_after` that calls `addMediaGalleryData()` on the collection while the module is enabled
  - `Model\Config\Source\LayoutType`, `ThumbPosition`, `ZoomType`: option sources (only `LayoutType` is used by `system.xml`)
- DI: `etc/frontend/di.xml` registers the module with `Panth\Core\ViewModel\ThemeConfig` (`registeredModules`) so `etc/theme-config.json` is picked up
- ACL resource: `Panth_ProductGallery::config` ("Panth Product Gallery Configuration") under `Magento_Config::config`
- Admin menu: `Panth_ProductGallery::group` ("Product Gallery") and `Panth_ProductGallery::settings` ("Configuration") under `Panth_Core::panth_extensions`
- No database tables, controllers, routes, web API endpoints, console commands or cron jobs are defined
- Translations: `i18n/en_US.csv`

## Uninstallation

```bash
bin/magento module:disable Panth_ProductGallery
composer remove mage2kishan/module-productgallery
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

The module creates no database tables. Values saved under `panth_productgallery/*` in `core_config_data` remain after removal and can be deleted manually if required.

## Support

- Product page: [kishansavaliya.com/magento-2-productgallery.html](https://kishansavaliya.com/magento-2-productgallery.html)
- Contact: [kishansavaliya.com/contact](https://kishansavaliya.com/contact)
- Email: kishansavaliyakb@gmail.com
- GitHub issues: [github.com/mage2sk/module-productgallery/issues](https://github.com/mage2sk/module-productgallery/issues)

## Documentation

See [USER_GUIDE.md](USER_GUIDE.md). It covers installation, verifying the module is active, configuration, gallery layouts, zoom, lightbox and navigation settings, widget usage, theme customization and troubleshooting.

## License

Commercial software license. See [LICENSE.txt](LICENSE.txt) in this repository.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Links

- Website: [kishansavaliya.com](https://kishansavaliya.com)
- All extensions: [kishansavaliya.com/magento-extensions.html](https://kishansavaliya.com/magento-extensions.html)
- GitHub: [github.com/mage2sk/module-productgallery](https://github.com/mage2sk/module-productgallery)
- Packagist: [packagist.org/packages/mage2kishan/module-productgallery](https://packagist.org/packages/mage2kishan/module-productgallery)
