<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Test\Unit\Observer;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Panth\ProductGallery\Helper\Data as ConfigHelper;
use Panth\ProductGallery\Observer\ProductCollectionLoadAfter;
use PHPUnit\Framework\TestCase;

class ProductCollectionLoadAfterTest extends TestCase
{
    private function configHelper(bool $enabled): ConfigHelper
    {
        $helper = $this->createStub(ConfigHelper::class);
        $helper->method('isEnabled')->willReturn($enabled);
        return $helper;
    }

    public function testExecuteDoesNothingWhenDisabled(): void
    {
        $eventObserver = $this->createMock(Observer::class);
        $eventObserver->expects($this->never())->method('getEvent');

        (new ProductCollectionLoadAfter($this->configHelper(false)))->execute($eventObserver);
    }

    public function testExecuteAddsMediaGalleryDataWhenEnabled(): void
    {
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())->method('addMediaGalleryData');

        $eventObserver = $this->createStub(Observer::class);
        $eventObserver->method('getEvent')->willReturn(new Event(['collection' => $collection]));

        (new ProductCollectionLoadAfter($this->configHelper(true)))->execute($eventObserver);
    }

    public function testExecuteToleratesEventWithoutCollection(): void
    {
        $eventObserver = $this->createMock(Observer::class);
        $eventObserver->expects($this->once())->method('getEvent')->willReturn(new Event([]));

        (new ProductCollectionLoadAfter($this->configHelper(true)))->execute($eventObserver);
    }
}
