<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Test\Unit\Block;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\Gallery as CoreGallery;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Panth\Core\Helper\Theme;
use Panth\ProductGallery\Block\Gallery;
use Panth\ProductGallery\Block\Widget\Gallery as WidgetGallery;
use Panth\ProductGallery\Helper\Data as ConfigHelper;
use Panth\ProductGallery\Test\Unit\Fixture\GalleryFixtureTrait;
use Panth\ProductGallery\ViewModel\Config as ConfigViewModel;
use PHPUnit\Framework\TestCase;

class GalleryTest extends TestCase
{
    use GalleryFixtureTrait;

    /**
     * @var mixed JSON-decodable string, a Throwable to throw, or null
     */
    private $coreJson = null;

    private function context(?Registry $registry = null): Context
    {
        $coreBlock = $this->createStub(CoreGallery::class);
        $coreBlock->method('getGalleryImagesJson')->willReturnCallback(function () {
            if ($this->coreJson instanceof \Throwable) {
                throw $this->coreJson;
            }
            return $this->coreJson;
        });
        $layout = $this->createStub(LayoutInterface::class);
        $layout->method('createBlock')->willReturn($coreBlock);

        $context = $this->createStub(Context::class);
        $context->method('getRegistry')->willReturn($registry ?? $this->createStub(Registry::class));
        $context->method('getLayout')->willReturn($layout);
        return $context;
    }

    private function block(
        ?ConfigHelper $config = null,
        ?Theme $theme = null,
        ?ImageHelper $imageHelper = null,
        ?Registry $registry = null
    ): Gallery {
        return new Gallery(
            $this->context($registry),
            $config ?? $this->sizedConfigHelper(),
            $theme ?? $this->createStub(Theme::class),
            $imageHelper ?? $this->imageHelper()
        );
    }

    private function theme(bool $hyva): Theme
    {
        $theme = $this->createStub(Theme::class);
        $theme->method('isHyva')->willReturn($hyva);
        return $theme;
    }

    public function testIsEnabledFollowsTheHelper(): void
    {
        $this->assertTrue($this->block($this->sizedConfigHelper(true))->isEnabled());
        $this->assertFalse($this->block($this->sizedConfigHelper(false))->isEnabled());
    }

    public function testGetTemplatePicksHyvaTemplateOnHyvaThemes(): void
    {
        $this->assertSame(
            'Panth_ProductGallery::hyva/gallery.phtml',
            $this->block(null, $this->theme(true))->getTemplate()
        );
    }

    public function testGetTemplatePicksLumaTemplateOtherwise(): void
    {
        $this->assertSame(
            'Panth_ProductGallery::gallery.phtml',
            $this->block(null, $this->theme(false))->getTemplate()
        );
    }

    public function testWidgetBlockInheritsThemeAwareTemplate(): void
    {
        $widget = new WidgetGallery(
            $this->context(),
            $this->sizedConfigHelper(),
            $this->theme(true),
            $this->imageHelper()
        );

        $this->assertInstanceOf(\Magento\Widget\Block\BlockInterface::class, $widget);
        $this->assertSame('Panth_ProductGallery::hyva/gallery.phtml', $widget->getTemplate());
    }

    public function testCurrentProductFallsBackToRegistry(): void
    {
        $product = $this->product([]);
        $registry = $this->createStub(Registry::class);
        $registry->method('registry')->willReturnMap([['product', $product]]);

        $this->assertSame($product, $this->block(null, null, null, $registry)->getCurrentProduct());
    }

    public function testGetGalleryImagesIsEmptyWithoutProduct(): void
    {
        $this->assertSame([], $this->block()->getGalleryImages());
    }

    public function testGetGalleryImagesIsEmptyWhenProductHasNoMediaGallery(): void
    {
        $block = $this->block();
        $block->setData('product', $this->product(null));

        $this->assertSame([], $block->getGalleryImages());
    }

    public function testGetGalleryImagesBuildsSizedUrlsAndFlagsMainImage(): void
    {
        $block = $this->block();
        $block->setData('product', $this->product([$this->image('/a.jpg', 1, 'Front')], '/a.jpg'));

        $this->assertSame(
            [[
                'thumb' => 'product_page_image_small|/a.jpg|72x64',
                'medium' => 'product_page_image_medium|/a.jpg|700x600',
                'large' => 'product_page_image_large|/a.jpg',
                'alt' => 'Front',
                'title' => 'Front',
                'position' => 1,
                'is_main' => true,
            ]],
            $block->getGalleryImages()
        );
    }

    public function testGetGalleryImagesSkipsDisabledAndSortsByPosition(): void
    {
        $block = $this->block();
        $block->setData('product', $this->product([
            $this->image('/c.jpg', 3, 'C'),
            $this->image('/off.jpg', 0, 'Off', true),
            $this->image('/a.jpg', 1, 'A'),
            $this->image('/b.jpg', 2, 'B'),
        ], '/b.jpg'));

        $images = $block->getGalleryImages();

        $this->assertSame(['A', 'B', 'C'], array_column($images, 'alt'));
        $this->assertSame([false, true, false], array_column($images, 'is_main'));
    }

    public function testAltFallsBackToProductNameWhenLabelIsEmpty(): void
    {
        $block = $this->block();
        $block->setData('product', $this->product([$this->image('/a.jpg', 1, '')]));

        $image = $block->getGalleryImages()[0];

        $this->assertSame('Blue Shirt', $image['alt']);
        $this->assertSame('Blue Shirt', $image['title']);
    }

    public function testCoreCaptionAndTitleWinOverRawLabel(): void
    {
        $this->coreJson = json_encode([
            ['caption' => 'Store caption', 'title' => 'Store title'],
            ['caption' => '', 'title' => ''],
        ]);
        $block = $this->block();
        $block->setData('product', $this->product([
            $this->image('/a.jpg', 1, 'raw a'),
            $this->image('/b.jpg', 2, 'raw b'),
        ]));

        $images = $block->getGalleryImages();

        $this->assertSame(['Store caption', 'raw b'], array_column($images, 'alt'));
        $this->assertSame(['Store title', 'raw b'], array_column($images, 'title'));
    }

    public function testCoreItemsStayAlignedWhenADisabledImageIsSkipped(): void
    {
        $this->coreJson = json_encode([
            ['caption' => 'first'],
            ['caption' => 'hidden'],
            ['caption' => 'third'],
        ]);
        $block = $this->block();
        $block->setData('product', $this->product([
            $this->image('/a.jpg', 1),
            $this->image('/b.jpg', 2, '', true),
            $this->image('/c.jpg', 3),
        ]));

        $this->assertSame(['first', 'third'], array_column($block->getGalleryImages(), 'alt'));
    }

    public function testCoreItemsAreIgnoredWhenTheirCountDiffers(): void
    {
        $this->coreJson = json_encode([['caption' => 'only one']]);
        $block = $this->block();
        $block->setData('product', $this->product([
            $this->image('/a.jpg', 1, 'A'),
            $this->image('/b.jpg', 2, 'B'),
        ]));

        $this->assertSame(['A', 'B'], array_column($block->getGalleryImages(), 'alt'));
    }

    public function testCoreItemsAreIgnoredWhenTheCoreBlockFails(): void
    {
        $this->coreJson = new \RuntimeException('boom');
        $block = $this->block();
        $block->setData('product', $this->product([$this->image('/a.jpg', 1, 'A')]));

        $this->assertSame(['A'], array_column($block->getGalleryImages(), 'alt'));
    }

    public function testCoreItemsAreIgnoredWhenJsonIsInvalid(): void
    {
        $this->coreJson = '{not json';
        $block = $this->block();
        $block->setData('product', $this->product([$this->image('/a.jpg', 1, 'A')]));

        $this->assertSame(['A'], array_column($block->getGalleryImages(), 'alt'));
    }

    public function testGalleryConfigComesFromTheHelper(): void
    {
        $this->assertSame(['layout_type' => 'horizontal'], $this->block()->getGalleryConfig());
    }

    public function testGalleryConfigJsonIsHtmlSafe(): void
    {
        $config = $this->createStub(ConfigHelper::class);
        $config->method('getGalleryConfig')->willReturn(['zoom_type' => '<x>&"\'']);

        $json = $this->block($config)->getGalleryConfigJson();

        foreach (['<', '>', '&', '"x', "'"] as $raw) {
            $this->assertStringNotContainsString($raw, $json);
        }
        $this->assertSame(['zoom_type' => '<x>&"\''], json_decode($json, true));
    }

    public function testToHtmlIsEmptyWithoutProduct(): void
    {
        $method = new \ReflectionMethod(Gallery::class, '_toHtml');

        $this->assertSame('', $method->invoke($this->block()));
    }

    private function renderingBlock(): Gallery
    {
        $block = $this->getMockBuilder(Gallery::class)
            ->setConstructorArgs([
                $this->context(),
                $this->sizedConfigHelper(),
                $this->theme(false),
                $this->imageHelper(),
            ])
            ->onlyMethods(['fetchView', 'getTemplateFile'])
            ->getMock();
        $block->method('getTemplateFile')->willReturn('/tmp/gallery.phtml');
        $block->expects($this->once())
            ->method('fetchView')
            ->with('/tmp/gallery.phtml')
            ->willReturn('<div class="gallery"></div>');
        return $block;
    }

    public function testToHtmlInjectsConfigViewModelBeforeRendering(): void
    {
        $block = $this->renderingBlock();
        $block->setData('product', $this->product([]));
        $method = new \ReflectionMethod(Gallery::class, '_toHtml');

        $this->assertSame('<div class="gallery"></div>', $method->invoke($block));
        $this->assertInstanceOf(ConfigViewModel::class, $block->getData('panth_gallery_viewmodel'));
    }

    public function testToHtmlKeepsAnExistingViewModel(): void
    {
        $existing = new \stdClass();
        $block = $this->renderingBlock();
        $block->setData('product', $this->product([]));
        $block->setData('view_model', $existing);
        $method = new \ReflectionMethod(Gallery::class, '_toHtml');
        $method->invoke($block);

        $this->assertNull($block->getData('panth_gallery_viewmodel'));
        $this->assertSame($existing, $block->getData('view_model'));
    }
}
