<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Test\Unit\ViewModel;

use Panth\ProductGallery\Helper\Data as ConfigHelper;
use Panth\ProductGallery\ViewModel\Config;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public static function delegatedProvider(): array
    {
        return [
            ['isEnabled', true],
            ['getLayoutType', 'vertical'],
            ['getThumbPosition', 'left'],
            ['getMainImageWidth', 1024],
            ['getMainImageHeight', 768],
            ['getThumbWidth', 64],
            ['getThumbHeight', 48],
            ['getVisibleThumbs', 6],
            ['isZoomEnabled', true],
            ['getZoomType', 'lens'],
            ['getZoomLevel', 4],
            ['isLightboxEnabled', true],
            ['showLightboxCounter', false],
            ['isKeyboardNavEnabled', true],
            ['showArrows', false],
            ['isSwipeEnabled', true],
            ['isInfiniteLoop', false],
            ['getGalleryConfig', ['layout_type' => 'grid']],
        ];
    }

    #[DataProvider('delegatedProvider')]
    public function testMethodsReturnTheHelperValue(string $method, $value): void
    {
        $helper = $this->createMock(ConfigHelper::class);
        $helper->expects($this->once())->method($method)->willReturn($value);

        $this->assertSame($value, (new Config($helper))->{$method}());
    }

    public function testGalleryConfigJsonEscapesHtmlSensitiveCharacters(): void
    {
        $config = [
            'zoom_type' => '</script><b>"a" & \'b\'',
            'zoom_level' => 3,
            'enabled' => true,
        ];
        $helper = $this->createStub(ConfigHelper::class);
        $helper->method('getGalleryConfig')->willReturn($config);

        $json = (new Config($helper))->getGalleryConfigJson();

        $this->assertStringNotContainsString('<', $json);
        $this->assertStringNotContainsString('>', $json);
        $this->assertStringNotContainsString('&', $json);
        $this->assertStringNotContainsString("'", $json);
        $this->assertStringContainsString('\\/script', $json);
        $this->assertSame($config, json_decode($json, true));
    }

    public function testGalleryConfigJsonReturnsEmptyStringWhenEncodingFails(): void
    {
        $helper = $this->createStub(ConfigHelper::class);
        $helper->method('getGalleryConfig')->willReturn(['bad' => "\xB1\x31"]);

        $this->assertSame('', (new Config($helper))->getGalleryConfigJson());
    }
}
