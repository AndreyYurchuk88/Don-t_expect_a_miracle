<?php

namespace Amasty\UserName\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class SearchSku extends \Magento\Framework\App\Action\Action
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var JsonFactory
     */
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
        $searchText = $this->getRequest()->getParam('search_text'); //получаем из запроса search_text

        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('sku', ['like' => sprintf('%s%%', $searchText)]) //фильтр по search_text
            ->setPageSize(10); //кол-во выводимых элементов на странице

        $results = []; //массив для хранения результатов поиска
        foreach ($collection as $product) {
            $results[] = [
                'sku' => $product->getSku(),
                'name' => $product->getName()
            ];
        }
        //возвращаем в json результаты поиска
        return $this->resultJsonFactory->create()->setData($results);
    }
}
