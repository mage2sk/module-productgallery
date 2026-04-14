<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 */
declare(strict_types=1);

namespace Panth\ProductGallery\Test\Unit\Observer;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Panth\ProductGallery\Helper\Data as ConfigHelper;
use Panth\ProductGallery\Observer\ProductCollectionLoadAfter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProductCollectionLoadAfterTest extends TestCase
{
    /**
     * @var ProductCollectionLoadAfter
     */
    private ProductCollectionLoadAfter $observer;

    /**
     * @var ConfigHelper|MockObject
     */
    private $configHelperMock;

    protected function setUp(): void
    {
        $this->configHelperMock = $this->createMock(ConfigHelper::class);
        $this->observer = new ProductCollectionLoadAfter($this->configHelperMock);
    }

    /**
     * Test observer does nothing when module is disabled
     */
    public function testExecuteWhenDisabled(): void
    {
        $this->configHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $eventObserverMock = $this->createMock(Observer::class);
        $eventObserverMock->expects($this->never())
            ->method('getEvent');

        $this->observer->execute($eventObserverMock);
    }

    /**
     * Test observer adds media gallery data when enabled
     */
    public function testExecuteWhenEnabled(): void
    {
        $this->configHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('addMediaGalleryData');

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('__call')
            ->with('getCollection')
            ->willReturn($collectionMock);

        $eventObserverMock = $this->createMock(Observer::class);
        $eventObserverMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->observer->execute($eventObserverMock);
    }

    /**
     * Test observer is properly instantiated
     */
    public function testObserverProperlyInstantiated(): void
    {
        $this->assertInstanceOf(ProductCollectionLoadAfter::class, $this->observer);
    }
}
