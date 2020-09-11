<?php

namespace Mobelop\Bridge\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mobelop\Bridge\Service\DataSync;

class ProductDelete implements ObserverInterface
{
    /**
     * @var DataSync
     */
    private $dataSync;

    /**
     * ProductSave constructor.
     * @param DataSync $dataSync
     */
    public function __construct(
        DataSync $dataSync
    ) {
        $this->dataSync = $dataSync;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->dataSync->postUpdate("delete", "product", $product->getSku(), $product->getStoreId());
    }
}
