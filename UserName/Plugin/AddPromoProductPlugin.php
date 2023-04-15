<?php

namespace Amasty\UserName\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Controller\Cart\Add;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;

class AddPromoProductPlugin
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ManagerInterface $messageManager
    )
    {
        $this->productRepository = $productRepository;
        $this->messageManager = $messageManager;
    }

    public function beforeExecute(Add $subject)   //объект класса Magento\Checkout\Controller\Cart\Add
    {   //если параметр product не определен, используем sku
        if (!$subject->getRequest()->getParam('product')) {
            try {
                $sku = (string)$subject->getRequest()->getParam('sku');
                $product = $this->productRepository->get($sku);
                $subject->getRequest()->setParams(['product' => $product->getId()]);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
    }
}
