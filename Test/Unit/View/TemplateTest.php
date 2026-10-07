<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Test\Unit\View;

use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Panth\ProductGallery\ViewModel\Config as ConfigViewModel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    private const HYVA = 'hyva/gallery.phtml';
    private const LUMA = 'gallery.phtml';

    private function escaper(): Escaper
    {
        $escaper = $this->createStub(Escaper::class);
        $html = static function ($value) {
            return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        };
        $escaper->method('escapeHtmlAttr')->willReturnCallback($html);
        $escaper->method('escapeUrl')->willReturnCallback($html);
        $escaper->method('escapeHtml')->willReturnCallback($html);
        $escaper->method('escapeJs')->willReturnCallback(static function ($value) {
            return preg_replace_callback('/[^a-zA-Z0-9,._]/u', static function ($match) {
                return sprintf('\\u%04X', mb_ord($match[0], 'UTF-8'));
            }, (string) $value);
        });
        return $escaper;
    }

    private function images(int $count, int $mainIndex = 0): array
    {
        $images = [];
        for ($i = 0; $i < $count; $i++) {
            $images[] = [
                'thumb' => 'https://example.com/thumb' . $i . '.jpg',
                'medium' => 'https://example.com/medium' . $i . '.jpg',
                'large' => 'https://example.com/large' . $i . '.jpg',
                'alt' => 'Shirt view ' . $i,
                'title' => 'Shirt view ' . $i,
                'position' => $i,
                'is_main' => $i === $mainIndex,
            ];
        }
        return $images;
    }

    private function config(array $overrides = []): array
    {
        return array_merge([
            'layout_type' => 'horizontal',
            'main_image_width' => 700,
            'main_image_height' => 600,
            'thumb_width' => 72,
            'thumb_height' => 64,
            'enable_zoom' => true,
            'zoom_level' => 3,
            'enable_lightbox' => true,
            'show_counter' => true,
            'enable_keyboard_nav' => true,
            'show_arrows' => true,
            'enable_swipe' => true,
            'infinite_loop' => false,
        ], $overrides);
    }

    private function render(string $template, ?array $images, array $config = [], bool $withViewModel = true): string
    {
        $block = new DataObject([
            'panth_gallery_images' => $images,
            'panth_gallery_config' => $this->config($config),
            'panth_gallery_viewmodel' => $withViewModel ? $this->createStub(ConfigViewModel::class) : null,
        ]);
        $escaper = $this->escaper();
        $file = dirname(__DIR__, 3) . '/view/frontend/templates/' . $template;
        $this->assertFileExists($file);

        $renderer = static function () use ($block, $escaper, $file) {
            ob_start();
            include $file;
            return (string) ob_get_clean();
        };
        return $renderer();
    }

    private function tagWith(string $html, string $needle): string
    {
        preg_match_all('/<(?:img|button|div)\b(?:[^>"]|"[^"]*")*>/s', $html, $matches);
        foreach ($matches[0] as $tag) {
            if (strpos($tag, $needle) !== false) {
                return $tag;
            }
        }
        $this->fail('No tag contains ' . $needle);
    }

    public static function templateProvider(): array
    {
        return [
            'hyva' => [self::HYVA],
            'luma' => [self::LUMA],
        ];
    }

    #[DataProvider('templateProvider')]
    public function testRendersNothingWithoutViewModel(string $template): void
    {
        $this->assertSame('', trim($this->render($template, $this->images(2), [], false)));
    }

    #[DataProvider('templateProvider')]
    public function testRendersNothingWithoutImages(string $template): void
    {
        $this->assertSame('', trim($this->render($template, [])));
        $this->assertSame('', trim($this->render($template, null)));
    }

    public function testHyvaMainImageIsServerRenderedWithSizeAndPriority(): void
    {
        $html = $this->render(self::HYVA, $this->images(3, 1));
        $main = $this->tagWith($html, 'class="pg-main-img"');

        $this->assertStringContainsString('src="https://example.com/medium1.jpg"', $main);
        $this->assertStringContainsString('alt="Shirt view 1"', $main);
        $this->assertStringContainsString('width="700"', $main);
        $this->assertStringContainsString('height="600"', $main);
        $this->assertStringContainsString('fetchpriority="high"', $main);
        $this->assertStringContainsString('loading="eager"', $main);
        $this->assertStringContainsString('x-init="init(', $html);
        $this->assertStringContainsString(', 1)"', $html);
    }

    public function testHyvaThumbnailsMarkTheMainImageAsCurrentAndSupportKeys(): void
    {
        $html = $this->render(self::HYVA, $this->images(3, 2));

        $this->assertSame(3, substr_count($html, 'aria-label="View image '));
        $this->assertSame(1, preg_match_all('/<button[^>]*\saria-current="true"/', $html));
        $current = $this->tagWith($html, 'aria-current="true"');
        $this->assertStringContainsString('goToImage(2)', $current);
        $this->assertStringContainsString('@keydown="thumbKey($event)"', $html);
        $this->assertStringContainsString("key === 'ArrowRight'", $html);
        $this->assertStringContainsString("key === 'Home'", $html);
        $this->assertStringContainsString('@keydown.arrow-left.prevent="prevImage()"', $html);
    }

    public function testHyvaArrowsAreTapSizedHiddenForSingleImagesAndDisabledAtTheEdges(): void
    {
        $html = $this->render(self::HYVA, $this->images(2));
        $prev = $this->tagWith($html, 'pg-arrow--prev');

        $this->assertStringContainsString('x-show="images.length > 1"', $prev);
        $this->assertStringContainsString(':disabled="!infiniteLoop && currentIndex <= 0"', $prev);
        $this->assertStringContainsString('width:44px;height:44px', $html);
        $this->assertStringContainsString('aria-label="Previous image"', $prev);

        $noArrows = $this->render(self::HYVA, $this->images(2), ['show_arrows' => false]);
        $this->assertStringNotContainsString('class="pg-arrow pg-arrow--prev"', $noArrows);
    }

    public function testHyvaVariantUpdateUsesTheFirstMainImage(): void
    {
        $html = $this->render(self::HYVA, $this->images(2));

        $this->assertStringContainsString('var mainIndex = -1;', $html);
        $this->assertStringContainsString('item.isMain && mainIndex < 0', $html);
        $this->assertStringContainsString('@update-gallery.window="updateGallery($event.detail)"', $html);
    }

    public function testHyvaGridRendersKeyboardReachableItemsWhenLightboxIsOn(): void
    {
        $html = $this->render(self::HYVA, $this->images(3), ['layout_type' => 'grid']);

        $this->assertStringContainsString('class="pg-grid"', $html);
        $this->assertSame(1, substr_count($html, 'pg-grid-item pg-grid-item--main'));
        $this->assertStringContainsString('<button class="pg-grid-item', $html);
        $this->assertStringContainsString('openLightbox(2)', $html);
        $this->assertStringNotContainsString('class="pg-main-img"', $html);
        $this->assertStringContainsString('@update-gallery.window', $html);
    }

    public function testHyvaGridUsesPlainItemsWhenLightboxIsOff(): void
    {
        $html = $this->render(self::HYVA, $this->images(3), ['layout_type' => 'grid', 'enable_lightbox' => false]);

        $this->assertStringContainsString('<div class="pg-grid-item', $html);
        $this->assertStringNotContainsString('<button class="pg-grid-item', $html);
        $this->assertStringNotContainsString('openLightbox(1)', $html);
        $this->assertStringNotContainsString('window.panthLightbox = {', $html);
    }

    public function testHyvaUnknownLayoutFallsBackToHorizontal(): void
    {
        $html = $this->render(self::HYVA, $this->images(2), ['layout_type' => 'carousel"><script>']);

        $this->assertStringContainsString('class="panth-gallery panth-gallery--horizontal"', $html);
        $this->assertStringNotContainsString('carousel', $html);
    }

    public function testHyvaVerticalLayoutStacksOnSmallScreens(): void
    {
        $html = $this->render(self::HYVA, $this->images(2), ['layout_type' => 'vertical']);

        $this->assertStringContainsString('panth-gallery--vertical', $html);
        $this->assertStringContainsString('flex-direction:column-reverse', $html);
        $this->assertStringContainsString('@media (min-width:1024px)', $html);
    }

    public function testHyvaZoomAndLightboxFlagsControlTheMarkup(): void
    {
        $on = $this->render(self::HYVA, $this->images(2));
        $this->assertStringContainsString('@pointermove="handleZoom($event)"', $on);
        $this->assertStringContainsString("event.pointerType !== 'mouse'", $on);
        $this->assertStringContainsString('role="button"', $on);
        $this->assertStringContainsString('aria-haspopup="dialog"', $on);
        $this->assertStringContainsString("setAttribute('aria-live', 'polite')", $on);

        $off = $this->render(self::HYVA, $this->images(2), ['enable_zoom' => false, 'enable_lightbox' => false]);
        $this->assertStringNotContainsString('handleZoom', $off);
        $this->assertStringNotContainsString('role="button"', $off);
        $this->assertStringNotContainsString('panth-lightbox-overlay', $off);
    }

    public function testHyvaEscapeAlwaysClosesTheLightboxEvenWithoutKeyboardNavigation(): void
    {
        $html = $this->render(self::HYVA, $this->images(2), ['enable_keyboard_nav' => false]);

        $this->assertStringContainsString("if (e.key === 'Escape') close();", $html);
        $this->assertStringNotContainsString("if (e.key === 'ArrowLeft') prev();", $html);
    }

    #[DataProvider('templateProvider')]
    public function testAltTextAndUrlsAreEscaped(string $template): void
    {
        $images = $this->images(2);
        $images[0]['alt'] = 'Tee "Pro" <b>';
        $html = $this->render($template, $images);

        $this->assertStringContainsString('alt="Tee &quot;Pro&quot; &lt;b&gt;"', $html);
        $this->assertStringNotContainsString('alt="Tee "Pro"', $html);
    }

    public function testLumaMainImageStartsOnTheMainImageWithSizeAndPriority(): void
    {
        $html = $this->render(self::LUMA, $this->images(3, 2));
        $main = $this->tagWith($html, '-main-img"');

        $this->assertStringContainsString('src="https://example.com/medium2.jpg"', $main);
        $this->assertStringContainsString('width="700"', $main);
        $this->assertStringContainsString('height="600"', $main);
        $this->assertStringContainsString('fetchpriority="high"', $main);
        $this->assertStringContainsString('var currentIndex = 2', $html);

        $current = $this->tagWith($html, 'aria-current="true"');
        $this->assertStringContainsString('data-index="2"', $current);
        $this->assertStringContainsString('panth-gallery__thumb--active', $current);
    }

    public function testLumaSingleImageForcesArrowsAndThumbsHidden(): void
    {
        $html = $this->render(self::LUMA, $this->images(1));

        $this->assertSame(2, substr_count($html, 'style="display:none !important"'));
        $this->assertStringContainsString('class="panth-gallery__thumbs" style="display:none"', $html);
        $this->assertStringContainsString("btn.style.setProperty('display', 'none', 'important')", $html);
    }

    public function testLumaArrowsAreDisabledAtTheEdgesUnlessLooping(): void
    {
        $first = $this->render(self::LUMA, $this->images(3, 0));
        $this->assertStringContainsString('disabled', $this->tagWith($first, 'panth-gallery__arrow--prev'));
        $this->assertStringNotContainsString('disabled', $this->tagWith($first, 'panth-gallery__arrow--next'));

        $last = $this->render(self::LUMA, $this->images(3, 2));
        $this->assertStringContainsString('disabled', $this->tagWith($last, 'panth-gallery__arrow--next'));

        $loop = $this->render(self::LUMA, $this->images(3, 0), ['infinite_loop' => true]);
        $this->assertStringNotContainsString('disabled', $this->tagWith($loop, 'panth-gallery__arrow--prev'));
    }

    public function testLumaSwatchUpdateUsesTheFirstMainImage(): void
    {
        $html = $this->render(self::LUMA, $this->images(2));

        $this->assertStringContainsString('var mainIndex = -1;', $html);
        $this->assertStringContainsString('img.isMain && mainIndex < 0', $html);
        $this->assertStringContainsString('goToImage(Math.max(0, mainIndex))', $html);
        $this->assertStringContainsString("data('gallery', galleryApi)", $html);
    }

    public function testLumaGridRendersAGridInsteadOfTheSlider(): void
    {
        $html = $this->render(self::LUMA, $this->images(3, 1), ['layout_type' => 'grid']);

        $this->assertStringContainsString('class="panth-gallery__grid"', $html);
        $this->assertSame(1, substr_count($html, 'panth-gallery__grid-item panth-gallery__grid-item--main'));
        $this->assertSame(3, substr_count($html, '<button class="panth-gallery__grid-item'));
        $this->assertStringNotContainsString('-main-img"', $html);
        $this->assertStringNotContainsString('class="panth-gallery__thumbs-track"', $html);
        $this->assertStringContainsString('var currentIndex = 0', $html);
    }

    public function testLumaVerticalLayoutPutsThumbnailsFirstOnWideScreens(): void
    {
        $html = $this->render(self::LUMA, $this->images(2), ['layout_type' => 'vertical']);

        $this->assertStringContainsString('panth-gallery--vertical', $html);
        $this->assertStringContainsString('.panth-gallery--vertical .panth-gallery__thumbs { order: -1;', $html);
    }

    public function testLumaUnknownLayoutFallsBackToHorizontal(): void
    {
        $html = $this->render(self::LUMA, $this->images(2), ['layout_type' => 'bogus']);

        $this->assertStringContainsString('panth-gallery--horizontal', $html);
        $this->assertStringNotContainsString('bogus', $html);
    }

    public function testLumaZoomIgnoresTouchAndLightboxFlagsControlMarkup(): void
    {
        $on = $this->render(self::LUMA, $this->images(2));
        $this->assertStringContainsString("e.pointerType !== 'mouse'", $on);
        $this->assertStringContainsString("mainImg.setAttribute('role', 'button')", $on);
        $this->assertStringContainsString("if (e.key === 'Escape') closeLb();", $on);

        $off = $this->render(self::LUMA, $this->images(2), [
            'enable_zoom' => false,
            'enable_lightbox' => false,
            'enable_swipe' => false,
        ]);
        $this->assertStringNotContainsString('pointermove', $off);
        $this->assertStringNotContainsString("mainImg.setAttribute('role', 'button')", $off);
        $this->assertStringNotContainsString('touchstart', $off);
    }
}
