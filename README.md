<!-- SEO Meta -->
<!--
  Title: Custom Product Image Gallery for Magento 2 | Zoom, Lightbox & Thumbnail Layouts | Panth Infotech
  Description: Panth Product Gallery for Magento 2 — configurable thumbnail layouts (horizontal, vertical, grid), inner zoom and lens zoom, fullscreen lightbox, responsive navigation, full Hyva + Luma support, and optional integration with Panth Advanced SEO for automatic image alt text. Compatible with Magento 2.4.4 - 2.4.8 and PHP 8.1 - 8.4.
  Keywords: magento 2 product gallery, magento 2 image zoom, magento 2 lightbox, magento 2 product images, magento 2 gallery widget, magento 2 thumbnail layout, hyva product gallery, luma product gallery
  Author: Kishan Savaliya (Panth Infotech)
  Canonical: https://github.com/mage2sk/module-productgallery
-->

# Custom Product Image Gallery for Magento 2 | Panth Product Gallery

[![Magento 2.4.4 - 2.4.8](https://img.shields.io/badge/Magento-2.4.4%20--%202.4.8-orange?logo=magento&logoColor=white)](https://magento.com)
[![PHP 8.1 - 8.4](https://img.shields.io/badge/PHP-8.1%20--%208.4-blue?logo=php&logoColor=white)](https://php.net)
[![Hyva + Luma](https://img.shields.io/badge/Themes-Hyva%20%2B%20Luma-14b8a6)]()
[![Packagist](https://img.shields.io/badge/Packagist-mage2kishan%2Fmodule--productgallery-orange?logo=packagist&logoColor=white)](https://packagist.org/packages/mage2kishan/module-productgallery)
[![Upwork Top Rated Plus](https://img.shields.io/badge/Upwork-Top%20Rated%20Plus-14a800?logo=upwork&logoColor=white)](https://www.upwork.com/freelancers/~016dd1767321100e21)
[![Panth Infotech Agency](https://img.shields.io/badge/Agency-Panth%20Infotech-14a800?logo=upwork&logoColor=white)](https://www.upwork.com/agencies/1881421506131960778/)
[![Website](https://img.shields.io/badge/Website-kishansavaliya.com-0D9488)](https://kishansavaliya.com)
[![Get a Quote](https://img.shields.io/badge/Get%20a%20Quote-Free%20Estimate-DC2626)](https://kishansavaliya.com/get-quote)

> **Custom Product Image Gallery for Magento 2** — replace the default product media gallery with a fast, configurable gallery supporting horizontal, vertical and grid thumbnail layouts, inner and lens image zoom, fullscreen lightbox, responsive navigation, full Hyva and Luma compatibility, and an optional soft dependency on Panth Advanced SEO for automatic image alt-text generation.

**Panth Product Gallery** gives merchants total control over how product images are presented on the product detail page. Choose between **horizontal thumbnails** (classic below-main-image strip), **vertical thumbnails** (side rail on desktop, strip on mobile), or a **grid layout** (Amazon/Etsy style). Enable **inner zoom** for hover magnification directly inside the main image, **lens zoom** for a floating magnifier window, or a **fullscreen lightbox** that opens the full-resolution image with swipe and keyboard navigation. Everything is responsive, touch-friendly, and tuned for Core Web Vitals on both **Hyva** (Alpine.js + Tailwind) and **Luma** (Knockout.js) storefronts. When **Panth Advanced SEO** is installed, the gallery automatically pulls AI-generated alt text into every `<img>` tag for better accessibility and SEO.

---

## 🚀 Need Custom Magento 2 Gallery or Product Page Work?

> **Get a free quote for your project in 24 hours** — custom gallery layouts, PDP redesigns, Hyva migration, performance optimization, and Adobe Commerce Cloud.

<p align="center">
  <a href="https://kishansavaliya.com/get-quote">
    <img src="https://img.shields.io/badge/Get%20a%20Free%20Quote%20%E2%86%92-Reply%20within%2024%20hours-DC2626?style=for-the-badge" alt="Get a Free Quote" />
  </a>
</p>

<table>
<tr>
<td width="50%" align="center">

### 🏆 Kishan Savaliya
**Top Rated Plus on Upwork**

[![Hire on Upwork](https://img.shields.io/badge/Hire%20on%20Upwork-Top%20Rated%20Plus-14a800?style=for-the-badge&logo=upwork&logoColor=white)](https://www.upwork.com/freelancers/~016dd1767321100e21)

100% Job Success • 10+ Years Magento Experience
Adobe Certified • Hyva Specialist

</td>
<td width="50%" align="center">

### 🏢 Panth Infotech Agency
**Magento Development Team**

[![Visit Agency](https://img.shields.io/badge/Visit%20Agency-Panth%20Infotech-14a800?style=for-the-badge&logo=upwork&logoColor=white)](https://www.upwork.com/agencies/1881421506131960778/)

Custom Modules • Theme Design • Migrations
Performance • SEO • Adobe Commerce Cloud

</td>
</tr>
</table>

**Visit our website:** [kishansavaliya.com](https://kishansavaliya.com) &nbsp;|&nbsp; **Get a quote:** [kishansavaliya.com/get-quote](https://kishansavaliya.com/get-quote)

---

## Table of Contents

- [Key Features](#key-features)
- [Compatibility](#compatibility)
- [Installation](#installation)
- [Configuration](#configuration)
- [Advanced SEO Integration](#advanced-seo-integration)
- [FAQ](#faq)
- [Support](#support)
- [About Panth Infotech](#about-panth-infotech)
- [Quick Links](#quick-links)

---

## Key Features

### Configurable Thumbnail Layouts

- **Horizontal layout** — traditional strip of thumbnails below the main image
- **Vertical layout** — thumbnails on the side rail (desktop), collapsing to a horizontal strip on mobile
- **Grid layout** — Amazon/Etsy-style multi-column grid showing all images at once
- **Thumbnail count & size** — configurable number of visible thumbnails and pixel dimensions
- **Active state styling** — border, shadow, and scale transitions for the selected thumbnail

### Image Zoom Modes

- **Inner zoom** — magnify the image in place on hover, no overlay required
- **Lens zoom** — floating magnifier lens with a side preview window
- **Fullscreen lightbox** — click to open the full-resolution image in a modal with swipe, pinch-zoom, and keyboard navigation
- **Zoom level control** — adjustable zoom factor (1.5x to 4x)
- **Touch-friendly** — pinch-zoom and swipe gestures on mobile and tablet

### Responsive Navigation

- Prev/next arrows with keyboard support (arrow keys, Escape to close)
- Dot indicators and image counter (e.g. "3 / 12")
- Swipe navigation on touch devices
- Auto-slide option with configurable interval
- Infinite loop or stop-at-ends behaviour

### Theme Support

- **Hyva compatible** — Alpine.js components, Tailwind CSS utility classes, no jQuery
- **Luma compatible** — Knockout.js bindings that cleanly replace the default `gallery.phtml`
- **Automatic theme detection** — via `Panth\Core\Helper\Theme`, the correct template is served without any manual switching
- **Child-theme safe** — templates can be overridden from your own custom theme

### SEO & Accessibility

- **Alt text from Panth Advanced SEO** — optional soft dependency; when installed, every image pulls its AI-generated alt text automatically
- **Semantic HTML** — proper `<figure>`, `<img>`, `aria-label`, and `role` attributes
- **Keyboard navigable** — full keyboard support (Tab, Enter, arrows, Escape)
- **Lazy loading** — `loading="lazy"` and `decoding="async"` on non-primary images
- **Structured image URLs** — preserves Magento media paths for CDN compatibility

### Performance

- **No jQuery on Hyva** — pure Alpine.js, minimal JS payload
- **CSS-only transitions** where possible — hardware-accelerated transforms
- **Lazy-loaded thumbnails** — off-screen thumbs defer loading
- **Optimized for Core Web Vitals** — minimal CLS, fast LCP on the main image

---

## Compatibility

| Requirement | Versions Supported |
|---|---|
| Magento Open Source | 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8 |
| Adobe Commerce | 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8 |
| Adobe Commerce Cloud | 2.4.4 — 2.4.8 |
| PHP | 8.1.x, 8.2.x, 8.3.x, 8.4.x |
| Hyva Theme | 1.3+ (fully supported) |
| Luma Theme | Native support |
| Panth_Core | Required (free) |
| Panth_AdvancedSEO | Optional (soft dependency for alt text) |

Tested on:
- Magento 2.4.8-p4 with PHP 8.4 (Hyva 1.3.x)
- Magento 2.4.7 with PHP 8.3 (Luma)
- Magento 2.4.6 with PHP 8.2

---

## Installation

### Composer Installation (Recommended)

```bash
composer require mage2kishan/module-productgallery
bin/magento module:enable Panth_Core Panth_ProductGallery
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

### Manual Installation via ZIP

1. Download the latest release ZIP from [Packagist](https://packagist.org/packages/mage2kishan/module-productgallery) or the [Adobe Commerce Marketplace](https://commercemarketplace.adobe.com)
2. Extract the contents to `app/code/Panth/ProductGallery/` in your Magento installation
3. Run the same commands as above starting from `bin/magento module:enable`

### Verify Installation

```bash
bin/magento module:status Panth_ProductGallery
# Expected output: Module is enabled
```

After installation, navigate to:
```
Admin → Stores → Configuration → Panth Extensions → Product Gallery
```

---

## Configuration

All settings live at `Stores → Configuration → Panth Extensions → Product Gallery`.

### General

| Setting | Default | Description |
|---|---|---|
| Enable Module | Yes | Master toggle for the custom gallery. Disable to fall back to Magento's default gallery. |
| Thumbnail Layout | Horizontal | Choose `Horizontal`, `Vertical`, or `Grid`. |
| Thumbnail Count | 5 | Visible thumbnails before scrolling (horizontal/vertical). |
| Thumbnail Size | 80px | Pixel dimensions for each thumbnail. |

### Zoom

| Setting | Default | Description |
|---|---|---|
| Zoom Mode | Inner | Choose `Inner`, `Lens`, `Lightbox`, or `Disabled`. |
| Zoom Level | 2.0x | Magnification factor (1.5x - 4x). |
| Enable Fullscreen Lightbox | Yes | Click to open the full-size image in a modal. |
| Enable Pinch Zoom (mobile) | Yes | Pinch-to-zoom inside the lightbox. |

### Navigation

| Setting | Default | Description |
|---|---|---|
| Show Prev/Next Arrows | Yes | Display navigation arrows on the main image. |
| Show Dot Indicators | No | Show a row of dot indicators below the main image. |
| Show Image Counter | Yes | Show "3 / 12" counter overlay. |
| Enable Keyboard Navigation | Yes | Arrow keys, Escape, Enter. |
| Enable Auto-Slide | No | Auto-advance the main image. |
| Auto-Slide Interval | 5s | Seconds between slides when auto-slide is on. |

---

## Advanced SEO Integration

Panth Product Gallery has a **soft dependency** on [Panth Advanced SEO](https://packagist.org/packages/mage2kishan/module-advanced-seo). When Advanced SEO is installed and enabled, the gallery automatically uses its AI-generated image alt text for every `<img>` tag — no manual alt text management required.

Without Advanced SEO, the gallery falls back to Magento's standard image label / product name pattern. You can still use the gallery fully without installing Advanced SEO; the integration is purely additive.

---

## FAQ

### Does this replace Magento's default product gallery?

Yes, on the product detail page. When the module is enabled, the default `gallery.phtml` is replaced with the Panth gallery (one template for Hyva, another for Luma — chosen automatically).

### Does it work with configurable product swatches?

Yes. When a customer picks a colour/size swatch, the gallery swaps to the correct variant images using Magento's standard `gallery` JSON payload.

### Does it work with video (YouTube / Vimeo)?

Yes. Magento product video entries are rendered inline with a play-button thumbnail; clicking opens the video in the lightbox.

### Is Hyva compatible?

Yes — fully. The module ships an Alpine.js implementation that does not depend on jQuery or Knockout, and installs automatically when the store theme is Hyva.

### Does it affect Core Web Vitals?

Positively. The Hyva implementation uses a minimal JS payload and lazy-loads thumbnails, which typically improves LCP and reduces CLS versus the default gallery.

### Can I override the template in my child theme?

Yes. Copy `view/frontend/templates/product/view/gallery.phtml` (Luma) or the equivalent file under the Hyva area into your own theme and customize freely.

### Does it support multi-store and multi-language?

Yes. All configuration respects Magento's scope hierarchy (default → website → store view) and all user-facing strings are translatable via Magento's `__()` function.

### Does it require Panth Advanced SEO?

No, it is an optional soft dependency. Advanced SEO only improves alt-text quality; the gallery works standalone.

---

## Support

| Channel | Contact |
|---|---|
| Email | kishansavaliyakb@gmail.com |
| Website | [kishansavaliya.com](https://kishansavaliya.com) |
| WhatsApp | +91 84012 70422 |
| GitHub Issues | [github.com/mage2sk/module-productgallery/issues](https://github.com/mage2sk/module-productgallery/issues) |
| Upwork (Top Rated Plus) | [Hire Kishan Savaliya](https://www.upwork.com/freelancers/~016dd1767321100e21) |
| Upwork Agency | [Panth Infotech](https://www.upwork.com/agencies/1881421506131960778/) |

Response time: 1-2 business days.

### 💼 Need Custom Magento Development?

Looking for **custom gallery layouts**, **PDP redesigns**, **Hyva migration**, or **performance optimization**? Get a free quote in 24 hours:

<p align="center">
  <a href="https://kishansavaliya.com/get-quote">
    <img src="https://img.shields.io/badge/%F0%9F%92%AC%20Get%20a%20Free%20Quote-kishansavaliya.com%2Fget--quote-DC2626?style=for-the-badge" alt="Get a Free Quote" />
  </a>
</p>

<p align="center">
  <a href="https://www.upwork.com/freelancers/~016dd1767321100e21">
    <img src="https://img.shields.io/badge/Hire%20Kishan-Top%20Rated%20Plus-14a800?style=for-the-badge&logo=upwork&logoColor=white" alt="Hire on Upwork" />
  </a>
  &nbsp;&nbsp;
  <a href="https://www.upwork.com/agencies/1881421506131960778/">
    <img src="https://img.shields.io/badge/Visit-Panth%20Infotech%20Agency-14a800?style=for-the-badge&logo=upwork&logoColor=white" alt="Visit Agency" />
  </a>
  &nbsp;&nbsp;
  <a href="https://kishansavaliya.com">
    <img src="https://img.shields.io/badge/Visit%20Website-kishansavaliya.com-0D9488?style=for-the-badge" alt="Visit Website" />
  </a>
</p>

---

## About Panth Infotech

Built and maintained by **Kishan Savaliya** — [kishansavaliya.com](https://kishansavaliya.com) — a **Top Rated Plus** Magento developer on Upwork with 10+ years of eCommerce experience.

**Panth Infotech** is a Magento 2 development agency specializing in high-quality, security-focused extensions and themes for both Hyva and Luma storefronts. Our extension suite covers SEO, performance, checkout, product presentation, customer engagement, and store management — over 34 modules built to MEQP standards and tested across Magento 2.4.4 to 2.4.8.

Browse the full extension catalog on the [Adobe Commerce Marketplace](https://commercemarketplace.adobe.com) or [Packagist](https://packagist.org/packages/mage2kishan/).

---

## Quick Links

- 🌐 **Website:** [kishansavaliya.com](https://kishansavaliya.com)
- 💬 **Get a Quote:** [kishansavaliya.com/get-quote](https://kishansavaliya.com/get-quote)
- 👨‍💻 **Upwork Profile (Top Rated Plus):** [upwork.com/freelancers/~016dd1767321100e21](https://www.upwork.com/freelancers/~016dd1767321100e21)
- 🏢 **Upwork Agency:** [upwork.com/agencies/1881421506131960778](https://www.upwork.com/agencies/1881421506131960778/)
- 📦 **Packagist:** [packagist.org/packages/mage2kishan/module-productgallery](https://packagist.org/packages/mage2kishan/module-productgallery)
- 🐙 **GitHub:** [github.com/mage2sk/module-productgallery](https://github.com/mage2sk/module-productgallery)
- 🛒 **Adobe Marketplace:** [commercemarketplace.adobe.com](https://commercemarketplace.adobe.com)
- 📧 **Email:** kishansavaliyakb@gmail.com
- 📱 **WhatsApp:** +91 84012 70422

---

<p align="center">
  <strong>Ready to upgrade your Magento 2 product pages?</strong><br/>
  <a href="https://kishansavaliya.com/get-quote">
    <img src="https://img.shields.io/badge/%F0%9F%9A%80%20Get%20Started%20%E2%86%92-Free%20Quote%20in%2024h-DC2626?style=for-the-badge" alt="Get Started" />
  </a>
</p>

---

**SEO Keywords:** magento 2 product gallery, magento 2 image zoom, magento 2 lightbox, magento 2 product images, magento 2 gallery widget, magento 2 thumbnail layout, hyva product gallery, luma product gallery, magento 2 product image slider, magento 2 fullscreen image, magento 2 zoom extension, magento 2 product media gallery, magento 2 inner zoom, magento 2 lens zoom, magento 2 product page gallery, magento 2 PDP gallery, magento 2 responsive gallery, magento 2 mobile gallery, magento 2 image alt text, magento 2 SEO gallery, panth product gallery, panth infotech, hire magento developer, top rated plus upwork, kishan savaliya magento, mage2kishan, mage2sk, magento 2.4.8 gallery, php 8.4 magento module, hyva gallery alpine js, luma gallery knockout, magento 2 product image customization
