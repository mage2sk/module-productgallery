<?php
declare(strict_types=1);

namespace Panth\ProductGallery\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Panth\ProductGallery\Helper\Data as ConfigHelper;

class ProductCollectionLoadAfter implements ObserverInterface
{
    private ConfigHelper $configHelper;

    public function __construct(
        ConfigHelper $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    public function execute(Observer $observer): void
    {
        if (!$this->configHelper->isEnabled()) {
            return;
        }

        $collection = $observer->getEvent()->getCollection();
        if ($collection) {
            $collection->addMediaGalleryData();
        }
    }
}
