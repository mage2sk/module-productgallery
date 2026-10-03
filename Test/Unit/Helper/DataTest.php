<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Test\Unit\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Panth\ProductGallery\Helper\Data;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var array
     */
    private array $calls = [];

    /**
     * Build a helper whose scope config answers from a path => value map.
     *
     * @param array $values
     * @param bool $enabled
     * @return Data
     */
    private function helper(array $values = [], bool $enabled = false): Data
    {
        $this->calls = [];
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturnCallback(
            function ($path, $scope = null, $storeId = null) use ($values) {
                $this->calls[] = [$path, $scope, $storeId];
                return $values[$path] ?? null;
            }
        );
        $scopeConfig->method('isSetFlag')->willReturn($enabled);

        $context = $this->createStub(Context::class);
        $context->method('getScopeConfig')->willReturn($scopeConfig);

        return new Data($context);
    }

    public function testIsEnabledReadsTheGeneralFlagAtStoreScope(): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with('panth_productgallery/general/enabled', ScopeInterface::SCOPE_STORE, 3)
            ->willReturn(true);
        $context = $this->createStub(Context::class);
        $context->method('getScopeConfig')->willReturn($scopeConfig);

        $this->assertTrue((new Data($context))->isEnabled(3));
    }

    public function testIsEnabledReturnsFalseWhenFlagIsOff(): void
    {
        $this->assertFalse($this->helper([], false)->isEnabled());
    }

    public function testGetConfigValuePrefixesThePathAndPassesStoreId(): void
    {
        $helper = $this->helper(['panth_productgallery/layout/layout_type' => 'grid']);

        $this->assertSame('grid', $helper->getConfigValue('layout/layout_type', 7));
        $this->assertSame(
            [['panth_productgallery/layout/layout_type', ScopeInterface::SCOPE_STORE, 7]],
            $this->calls
        );
    }

    public function testTypedGettersForwardTheStoreId(): void
    {
        $helper = $this->helper();
        $helper->getThumbWidth(4);
        $helper->isInfiniteLoop(4);

        $this->assertSame([4, 4], array_column($this->calls, 2));
    }

    public static function defaultsProvider(): array
    {
        return [
            'layout type' => ['getLayoutType', 'horizontal'],
            'thumb position' => ['getThumbPosition', 'bottom'],
            'main width' => ['getMainImageWidth', 700],
            'main height' => ['getMainImageHeight', 700],
            'thumb width' => ['getThumbWidth', 72],
            'thumb height' => ['getThumbHeight', 72],
            'visible thumbs' => ['getVisibleThumbs', 5],
            'zoom type' => ['getZoomType', 'inner'],
            'zoom level' => ['getZoomLevel', 3],
            'zoom enabled' => ['isZoomEnabled', false],
            'lightbox' => ['isLightboxEnabled', false],
            'counter' => ['showLightboxCounter', false],
            'keyboard' => ['isKeyboardNavEnabled', false],
            'arrows' => ['showArrows', false],
            'swipe' => ['isSwipeEnabled', false],
            'loop' => ['isInfiniteLoop', false],
        ];
    }

    #[DataProvider('defaultsProvider')]
    public function testGettersFallBackToDefaultsWhenUnset(string $method, $expected): void
    {
        $this->assertSame($expected, $this->helper()->{$method}());
    }

    public static function configuredProvider(): array
    {
        return [
            'layout type' => ['getLayoutType', 'layout/layout_type', 'vertical', 'vertical'],
            'thumb position' => ['getThumbPosition', 'layout/thumb_position', 'left', 'left'],
            'main width' => ['getMainImageWidth', 'layout/main_image_width', '1200', 1200],
            'main height' => ['getMainImageHeight', 'layout/main_image_height', '900', 900],
            'thumb width' => ['getThumbWidth', 'layout/thumb_width', '100', 100],
            'thumb height' => ['getThumbHeight', 'layout/thumb_height', '80', 80],
            'visible thumbs' => ['getVisibleThumbs', 'layout/visible_thumbs', '8', 8],
            'zoom type' => ['getZoomType', 'zoom/zoom_type', 'lens', 'lens'],
            'zoom level' => ['getZoomLevel', 'zoom/zoom_level', '4', 4],
            'zoom enabled' => ['isZoomEnabled', 'zoom/enable_zoom', '1', true],
            'lightbox' => ['isLightboxEnabled', 'lightbox/enable_lightbox', '1', true],
            'counter' => ['showLightboxCounter', 'lightbox/show_counter', '1', true],
            'keyboard' => ['isKeyboardNavEnabled', 'lightbox/enable_keyboard_nav', '1', true],
            'arrows' => ['showArrows', 'navigation/show_arrows', '1', true],
            'swipe' => ['isSwipeEnabled', 'navigation/enable_swipe', '1', true],
            'loop' => ['isInfiniteLoop', 'navigation/infinite_loop', '1', true],
        ];
    }

    #[DataProvider('configuredProvider')]
    public function testGettersReturnConfiguredValuesCast(string $method, string $path, $raw, $expected): void
    {
        $helper = $this->helper(['panth_productgallery/' . $path => $raw]);

        $this->assertSame($expected, $helper->{$method}());
    }

    public function testZeroOrEmptyDimensionsFallBackToDefaults(): void
    {
        $helper = $this->helper([
            'panth_productgallery/layout/main_image_width' => '0',
            'panth_productgallery/layout/thumb_height' => '',
            'panth_productgallery/layout/visible_thumbs' => '0',
        ]);

        $this->assertSame(700, $helper->getMainImageWidth());
        $this->assertSame(72, $helper->getThumbHeight());
        $this->assertSame(5, $helper->getVisibleThumbs());
    }

    public function testBooleanFlagsTreatZeroStringAsDisabled(): void
    {
        $helper = $this->helper(['panth_productgallery/zoom/enable_zoom' => '0']);

        $this->assertFalse($helper->isZoomEnabled());
    }

    public static function zoomLevelProvider(): array
    {
        return [
            'above max' => ['10', 5],
            'max' => ['5', 5],
            'min' => ['2', 2],
            'below min' => ['1', 2],
            'negative' => ['-4', 2],
            'zero uses default' => ['0', 3],
        ];
    }

    #[DataProvider('zoomLevelProvider')]
    public function testZoomLevelIsClampedBetweenTwoAndFive(string $raw, int $expected): void
    {
        $helper = $this->helper(['panth_productgallery/zoom/zoom_level' => $raw]);

        $this->assertSame($expected, $helper->getZoomLevel());
    }

    public function testGetGalleryConfigAggregatesEveryValue(): void
    {
        $helper = $this->helper(
            [
                'panth_productgallery/layout/layout_type' => 'grid',
                'panth_productgallery/layout/thumb_position' => 'right',
                'panth_productgallery/layout/main_image_width' => '800',
                'panth_productgallery/layout/main_image_height' => '600',
                'panth_productgallery/layout/thumb_width' => '90',
                'panth_productgallery/layout/thumb_height' => '60',
                'panth_productgallery/layout/visible_thumbs' => '4',
                'panth_productgallery/zoom/enable_zoom' => '1',
                'panth_productgallery/zoom/zoom_type' => 'lens',
                'panth_productgallery/zoom/zoom_level' => '9',
                'panth_productgallery/lightbox/enable_lightbox' => '1',
                'panth_productgallery/lightbox/show_counter' => '0',
                'panth_productgallery/lightbox/enable_keyboard_nav' => '1',
                'panth_productgallery/navigation/show_arrows' => '1',
                'panth_productgallery/navigation/enable_swipe' => '0',
                'panth_productgallery/navigation/infinite_loop' => '1',
            ],
            true
        );

        $this->assertSame(
            [
                'enabled' => true,
                'layout_type' => 'grid',
                'thumb_position' => 'right',
                'main_image_width' => 800,
                'main_image_height' => 600,
                'thumb_width' => 90,
                'thumb_height' => 60,
                'visible_thumbs' => 4,
                'enable_zoom' => true,
                'zoom_type' => 'lens',
                'zoom_level' => 5,
                'enable_lightbox' => true,
                'show_counter' => false,
                'enable_keyboard_nav' => true,
                'show_arrows' => true,
                'enable_swipe' => false,
                'infinite_loop' => true,
            ],
            $helper->getGalleryConfig()
        );
    }
}
