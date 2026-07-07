<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Test\Unit\Block;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Registry;
use Panth\Core\Helper\Theme;
use Panth\ProductGallery\Block\Gallery;
use Panth\ProductGallery\Helper\Data as ConfigHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GalleryTest extends TestCase
{
    private Gallery $block;

    private $configHelperMock;

    private $themeHelperMock;

    protected function setUp(): void
    {
        $contextMock = $this->createMock(Context::class);
        $registryMock = $this->createMock(Registry::class);
        $contextMock->method('getRegistry')->willReturn($registryMock);

        $this->configHelperMock = $this->createMock(ConfigHelper::class);
        $this->themeHelperMock = $this->createMock(Theme::class);
        $imageHelperMock = $this->createMock(ImageHelper::class);

        $this->block = new Gallery(
            $contextMock,
            $this->configHelperMock,
            $this->themeHelperMock,
            $imageHelperMock
        );
    }

    public function testIsEnabledReturnsTrue(): void
    {
        $this->configHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->assertTrue($this->block->isEnabled());
    }

    public function testIsEnabledReturnsFalse(): void
    {
        $this->configHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->assertFalse($this->block->isEnabled());
    }

    public function testGetTemplateReturnsHyvaTemplate(): void
    {
        $this->themeHelperMock->expects($this->once())
            ->method('isHyva')
            ->willReturn(true);

        $this->assertEquals('Panth_ProductGallery::hyva/gallery.phtml', $this->block->getTemplate());
    }

    public function testGetTemplateReturnsLumaTemplate(): void
    {
        $this->themeHelperMock->expects($this->once())
            ->method('isHyva')
            ->willReturn(false);

        $this->assertEquals('Panth_ProductGallery::gallery.phtml', $this->block->getTemplate());
    }
}
