<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 *
 * Ensures media gallery entries are loaded for products in collections.
 */
declare(strict_types=1);

namespace Panth\ProductGallery\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Panth\ProductGallery\Helper\Data as ConfigHelper;

/**
 * Observer to ensure product collections include media gallery data
 */
class ProductCollectionLoadAfter implements ObserverInterface
{
    /**
     * @var ConfigHelper
     */
    private ConfigHelper $configHelper;

    /**
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        ConfigHelper $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * Add media gallery entries to loaded product collection
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if (!$this->configHelper->isEnabled()) {
            return;
        }

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $observer->getEvent()->getCollection();
        if ($collection) {
            $collection->addMediaGalleryData();
        }
    }
}
