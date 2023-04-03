<?php

namespace Amasty\UserName\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $collectionFactory;
    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $searchText = $this->getRequest()->getParam('search_text');
        if (strlen($searchText) < 3) {
            return $this->resultJsonFactory->create()->setData([]);
        }

        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('sku', ['like' => "{$searchText}%"])
            ->setPageSize(10);

        $results = [];
        foreach ($collection as $product) {
            $results[] = [
                'sku' => $product->getSku(),
                'name' => $product->getName()
            ];
        }

        return $this->resultJsonFactory->create()->setData($results);
    }
}
