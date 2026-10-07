<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Test\Unit\Plugin;

use Magento\Catalog\Block\Product\View\Gallery as DefaultGallery;
use Magento\Framework\DataObject;
use Panth\Core\Helper\Theme;
use Panth\ProductGallery\Helper\Data as ConfigHelper;
use Panth\ProductGallery\Plugin\HideDefaultGallery;
use Panth\ProductGallery\Test\Unit\Fixture\GalleryFixtureTrait;
use Panth\ProductGallery\ViewModel\Config as ConfigViewModel;
use PHPUnit\Framework\TestCase;

class HideDefaultGalleryTest extends TestCase
{
    use GalleryFixtureTrait;

    /**
     * @var \stdClass
     */
    private \stdClass $state;

    /**
     * @var callable|null Replaces the subject's toHtml() rendering
     */
    private $render = null;

    protected function setUp(): void
    {
        $this->render = null;
    }

    private function plugin(bool $enabled = true, bool $hyva = false, ?ConfigViewModel $viewModel = null): HideDefaultGallery
    {
        $theme = $this->createStub(Theme::class);
        $theme->method('isHyva')->willReturn($hyva);
        if ($viewModel === null) {
            $viewModel = $this->createStub(ConfigViewModel::class);
            $viewModel->method('getGalleryConfig')->willReturn(['layout_type' => 'grid']);
        }

        return new HideDefaultGallery($this->sizedConfigHelper($enabled), $theme, $viewModel, $this->imageHelper());
    }

    /**
     * @param string $name
     * @param DataObject|null $product
     * @param string|null $coreJson
     * @return DefaultGallery
     */
    private function subject(string $name, ?DataObject $product, ?string $coreJson = null): DefaultGallery
    {
        $state = new \stdClass();
        $state->template = 'Magento_Catalog::product/view/gallery.phtml';
        $state->templatesDuringRender = [];
        $state->data = [];
        $state->renders = 0;
        $this->state = $state;

        $subject = $this->createStub(DefaultGallery::class);
        $subject->method('getNameInLayout')->willReturn($name);
        $subject->method('getProduct')->willReturn($product);
        $subject->method('getGalleryImagesJson')->willReturn($coreJson);
        $subject->method('getTemplate')->willReturnCallback(static fn() => $state->template);
        $subject->method('setTemplate')->willReturnCallback(
            static function ($template) use ($state, &$subject) {
                $state->template = $template;
                return $subject;
            }
        );
        $subject->method('setData')->willReturnCallback(
            static function ($key, $value = null) use ($state, &$subject) {
                $state->data[$key] = $value;
                return $subject;
            }
        );
        $subject->method('toHtml')->willReturnCallback(function () use ($state, &$subject) {
            $state->renders++;
            $state->templatesDuringRender[] = $state->template;
            if ($this->render !== null) {
                return ($this->render)($subject);
            }
            return '<div class="panth-gallery"></div>';
        });

        return $subject;
    }

    private function galleryProduct(int $id = 5): DataObject
    {
        return $this->product([$this->image('/b.jpg', 2, 'Back'), $this->image('/a.jpg', 1, 'Front')], '/a.jpg', $id);
    }

    public function testDisabledModuleLeavesOutputUntouched(): void
    {
        $subject = $this->subject('product.media', $this->galleryProduct());

        $this->assertSame('core', $this->plugin(false)->afterToHtml($subject, 'core'));
        $this->assertSame(0, $this->state->renders);
    }

    public function testUnrelatedBlocksAreNotReplaced(): void
    {
        $subject = $this->subject('product.info.details', $this->galleryProduct());

        $this->assertSame('core', $this->plugin()->afterToHtml($subject, 'core'));
        $this->assertSame(0, $this->state->renders);
    }

    public function testMissingProductOrProductIdKeepsCoreOutput(): void
    {
        $plugin = $this->plugin();

        $this->assertSame('core', $plugin->afterToHtml($this->subject('product.media', null), 'core'));
        $this->assertSame(
            'core',
            $plugin->afterToHtml($this->subject('product.media', $this->product([$this->image('/a.jpg', 1)], '/a.jpg', 0)), 'core')
        );
        $this->assertSame(0, $this->state->renders);
    }

    public function testProductWithoutImagesKeepsCoreOutputAndTemplate(): void
    {
        $subject = $this->subject('product.media', $this->product(null));

        $this->assertSame('core', $this->plugin()->afterToHtml($subject, 'core'));
        $this->assertSame(0, $this->state->renders);
        $this->assertSame('Magento_Catalog::product/view/gallery.phtml', $this->state->template);
    }

    public function testProductWithoutImagesGetsPlaceholderInEmptyCoreImage(): void
    {
        $subject = $this->subject('product.info.media.image', $this->product(null));
        $core = '<div class="gallery-placeholder"><img alt="main product photo" class="gallery-placeholder__image" src="" width="700" />'
            . '<link href=""></div><script>{"data": []}</script>';

        $html = $this->plugin()->afterToHtml($subject, $core);

        $this->assertStringContainsString(
            'class="gallery-placeholder__image" src="https://example.com/static/placeholder/image.jpg"',
            $html
        );
        $this->assertStringContainsString('<link href="https://example.com/static/placeholder/image.jpg">', $html);
        $this->assertStringNotContainsString('src=""', $html);
        $this->assertSame(0, $this->state->renders);
    }

    public function testFilledCoreImageSourceIsNotChanged(): void
    {
        $subject = $this->subject('product.info.media.image', $this->product(null));
        $core = '<img class="gallery-placeholder__image" src="https://example.com/a.jpg" /><link href="https://example.com/a.jpg">';

        $this->assertSame($core, $this->plugin()->afterToHtml($subject, $core));
    }

    public function testOtherEmptyImagesOutsideThePlaceholderAreNotChanged(): void
    {
        $subject = $this->subject('product.media', $this->product(null));
        $core = '<img class="other" src="" />';

        $this->assertSame($core, $this->plugin()->afterToHtml($subject, $core));
    }

    public function testOnlyDisabledImagesKeepCoreOutput(): void
    {
        $subject = $this->subject('product.media', $this->product([$this->image('/a.jpg', 1, 'A', true)]));

        $this->assertSame('core', $this->plugin()->afterToHtml($subject, 'core'));
        $this->assertSame(0, $this->state->renders);
    }

    public function testReplacesProductMediaWithLumaTemplateAndRestoresOriginal(): void
    {
        $viewModel = $this->createStub(ConfigViewModel::class);
        $viewModel->method('getGalleryConfig')->willReturn(['layout_type' => 'grid']);
        $subject = $this->subject('product.media', $this->galleryProduct());

        $html = $this->plugin(true, false, $viewModel)->afterToHtml($subject, 'core');

        $this->assertSame('<div class="panth-gallery"></div>', $html);
        $this->assertSame(['Panth_ProductGallery::gallery.phtml'], $this->state->templatesDuringRender);
        $this->assertSame('Magento_Catalog::product/view/gallery.phtml', $this->state->template);
        $this->assertSame(['layout_type' => 'grid'], $this->state->data['panth_gallery_config']);
        $this->assertSame($viewModel, $this->state->data['panth_gallery_viewmodel']);
    }

    public function testUsesHyvaTemplateOnHyvaThemesForTheImageBlock(): void
    {
        $subject = $this->subject('product.info.media.image', $this->galleryProduct());

        $this->plugin(true, true)->afterToHtml($subject, 'core');

        $this->assertSame(['Panth_ProductGallery::hyva/gallery.phtml'], $this->state->templatesDuringRender);
    }

    public function testImagesPassedToTemplateAreSortedSizedAndLabelled(): void
    {
        $subject = $this->subject(
            'product.media',
            $this->product([
                $this->image('/c.jpg', 3, ''),
                $this->image('/a.jpg', 1, 'raw a'),
                $this->image('/off.jpg', 0, 'off', true),
            ], '/c.jpg'),
            (string) json_encode([
                ['caption' => '', 'title' => 'Side view'],
                ['caption' => 'Caption A', 'title' => ''],
                ['caption' => 'hidden'],
            ])
        );

        $this->plugin()->afterToHtml($subject, 'core');

        $this->assertSame(
            [
                [
                    'thumb' => 'product_page_image_small|/a.jpg|72x64',
                    'medium' => 'product_page_image_medium|/a.jpg|700x600',
                    'large' => 'product_page_image_large|/a.jpg',
                    'alt' => 'Caption A',
                    'title' => 'Caption A',
                    'position' => 1,
                    'is_main' => false,
                ],
                [
                    'thumb' => 'product_page_image_small|/c.jpg|72x64',
                    'medium' => 'product_page_image_medium|/c.jpg|700x600',
                    'large' => 'product_page_image_large|/c.jpg',
                    'alt' => 'Blue Shirt',
                    'title' => 'Side view',
                    'position' => 3,
                    'is_main' => true,
                ],
            ],
            $this->state->data['panth_gallery_images']
        );
    }

    public function testCoreItemsWithMismatchedCountAreIgnored(): void
    {
        $subject = $this->subject(
            'product.media',
            $this->product([$this->image('/a.jpg', 1, 'Raw')]),
            (string) json_encode([['caption' => 'x'], ['caption' => 'y']])
        );

        $this->plugin()->afterToHtml($subject, 'core');

        $this->assertSame('Raw', $this->state->data['panth_gallery_images'][0]['alt']);
    }

    public function testVideoBlockIsHiddenOnlyAfterTheSameProductWasReplaced(): void
    {
        $plugin = $this->plugin();
        $product = $this->galleryProduct(5);

        $this->assertSame('video', $plugin->afterToHtml($this->subject('product.info.media.video', $product), 'video'));

        $plugin->afterToHtml($this->subject('product.media', $product), 'core');

        $this->assertSame('', $plugin->afterToHtml($this->subject('product.info.media.video', $product), 'video'));
        $this->assertSame(
            'video',
            $plugin->afterToHtml($this->subject('product.info.media.video', $this->galleryProduct(9)), 'video')
        );
        $this->assertSame('video', $plugin->afterToHtml($this->subject('product.info.media.video', null), 'video'));
    }

    public function testEmptyRenderKeepsCoreOutputAndDoesNotHideVideo(): void
    {
        $plugin = $this->plugin();
        $product = $this->galleryProduct();
        $this->render = static fn() => '';
        $subject = $this->subject('product.media', $product);

        $this->assertSame('core', $plugin->afterToHtml($subject, 'core'));
        $this->assertSame('Magento_Catalog::product/view/gallery.phtml', $this->state->template);
        $this->assertSame('video', $plugin->afterToHtml($this->subject('product.info.media.video', $product), 'video'));
    }

    public function testRenderingExceptionFallsBackToCoreOutputAndResetsGuard(): void
    {
        $plugin = $this->plugin();
        $this->render = static function () {
            throw new \RuntimeException('template failure');
        };

        $this->assertSame('core', $plugin->afterToHtml($this->subject('product.media', $this->galleryProduct()), 'core'));

        $this->render = null;
        $this->assertSame(
            '<div class="panth-gallery"></div>',
            $plugin->afterToHtml($this->subject('product.media', $this->galleryProduct()), 'core')
        );
    }

    public function testRenderingExceptionRestoresOriginalTemplate(): void
    {
        $this->render = static function () {
            throw new \RuntimeException('template failure');
        };

        $this->assertSame('core', $this->plugin()->afterToHtml($this->subject('product.media', $this->galleryProduct()), 'core'));
        $this->assertSame('Magento_Catalog::product/view/gallery.phtml', $this->state->template);
    }

    public function testNestedCallDuringRenderingIsPassedThrough(): void
    {
        $plugin = $this->plugin();
        $nested = null;
        $this->render = static function ($subject) use ($plugin, &$nested) {
            $nested = $plugin->afterToHtml($subject, 'inner core');
            return '<div>outer</div>';
        };

        $this->assertSame('<div>outer</div>', $plugin->afterToHtml($this->subject('product.media', $this->galleryProduct()), 'core'));
        $this->assertSame('inner core', $nested);
        $this->assertSame(1, $this->state->renders);
    }
}
