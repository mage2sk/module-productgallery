<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Test\Unit\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Panth\ProductGallery\Helper\Data;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    private Data $helper;

    private $scopeConfigMock;

    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);

        $contextMock = $this->createMock(Context::class);
        $contextMock->method('getScopeConfig')->willReturn($this->scopeConfigMock);

        $this->helper = new Data($contextMock);
    }

    public function testIsEnabledReturnsFalse(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                'panth_productgallery/general/enabled',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(false);

        $this->assertFalse($this->helper->isEnabled());
    }

    public function testIsEnabledReturnsTrue(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                'panth_productgallery/general/enabled',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(true);

        $this->assertTrue($this->helper->isEnabled());
    }

    public function testGetLayoutTypeDefault(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                'panth_productgallery/layout/layout_type',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(null);

        $this->assertEquals('horizontal', $this->helper->getLayoutType());
    }

    public function testGetLayoutTypeConfigured(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                'panth_productgallery/layout/layout_type',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn('vertical');

        $this->assertEquals('vertical', $this->helper->getLayoutType());
    }

    public function testGetZoomLevelClamped(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(
                'panth_productgallery/zoom/zoom_level',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn('10');

        $this->assertEquals(5, $this->helper->getZoomLevel());
    }

    public function testGetGalleryConfigReturnsArray(): void
    {
        $this->scopeConfigMock->method('isSetFlag')->willReturn(true);
        $this->scopeConfigMock->method('getValue')->willReturn(null);

        $config = $this->helper->getGalleryConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('enabled', $config);
        $this->assertArrayHasKey('layout_type', $config);
        $this->assertArrayHasKey('enable_zoom', $config);
        $this->assertArrayHasKey('enable_lightbox', $config);
    }
}
