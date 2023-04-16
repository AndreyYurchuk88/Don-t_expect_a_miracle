<?php

namespace Amasty\UserName\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Amasty\UserName\Model\BlacklistRepository;

class AddToCart implements ActionInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var EventManagerInterface
     */

    protected $eventManager;

    /**
     * @var BlacklistRepository
     */
    protected $blacklistRepository;


    public function __construct(
        Context                    $context,
        RequestInterface           $request,
        RedirectFactory            $resultRedirectFactory,
        ProductRepositoryInterface $productRepository,
        ManagerInterface           $messageManager,
        CheckoutSession            $checkoutSession,
        EventManagerInterface      $eventManager,
        BlacklistRepository        $blacklistRepository
    )
    {
        $this->context = $context;
        $this->request = $request;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->productRepository = $productRepository;
        $this->messageManager = $messageManager;
        $this->checkoutSession = $checkoutSession;
        $this->eventManager = $eventManager;
        $this->blacklistRepository = $blacklistRepository;
    }

    public function execute()
    {
        $sku = $this->request->getParam('sku');
        $qty = (int)$this->request->getParam('qty');

        // Проверка на заполнение полей sku и qty
        if (!$sku || !$qty) {
            $this->messageManager->addError(('Please specify product and quantity.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        // Получаем квоту
        $quote = $this->checkoutSession->getQuote();
        if (!$quote->getId()) {
            $quote->save();
        }

        try {
            $product = $this->productRepository->get($sku);

            // Проверка на наличие такого товара
            if (!$product->getId()) {
                throw new LocalizedException(('Product not found.'));
            }

            // Проверка на simple товар
            if ($product->getTypeId() !== Type::TYPE_SIMPLE) {
                throw new LocalizedException(('This product is not available.'));
            }

            // Проверка qty на положительное число
            if ($qty < 1) {
                throw new LocalizedException(('Qty must be a positive number.'));
            }

            // Получаем данные о количестве товара
            $productStockData = $product->getQuantityAndStockStatus();

            // Проверка на наличие товара в достаточном количестве
            if (!$productStockData['is_in_stock'] || $productStockData['qty'] < $qty) {
                throw new LocalizedException(('Not enough quantity available.'));
            }

            //Обращаемся к репозиторию
            $blacklistSku = $this->blacklistRepository->getBySku($sku);

            //Есть ли товар в Blacklist
            if ($blacklistSku->getData()) {
                //Количество товара, который уже добавлен в корзину
                $productCart = $quote->getItemByProduct($product);
                $productCart = $productCart ? $productCart->getQty() : 0; //Если есть обьект товара = $productCart, else = 0
                $resultQty = $qty + $productCart; //Общее количество продукта, которое будет в корзине
                $blacklistSkuQty = $blacklistSku->getQty(); //Доступное кол-во в Blacklist
                //Если товара достаточно или равно
                if ($blacklistSkuQty >= $resultQty) {
                    $this->addProduct($quote, $product, $qty, $sku);
                } else {
                    //Если меньше, чем запрошенное количество
                    $lastQty = $blacklistSkuQty - $productCart;
                    if ($lastQty > 0) {
                        $this->addProduct($quote, $product, $lastQty, $sku);
                        $this->messageManager->addWarningMessage("Too much requested, only added $blacklistSkuQty items");
                        //Если нету товара
                    } else {
                        $this->messageManager->addWarningMessage("Sorry nothing has been added");
                    }
                }
            } else {
                $this->addProduct($quote, $product, $qty, $sku);
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
    public function addProduct($quote, $product, $qty, $sku){
        $quote->addProduct($product, $qty);
        $quote->save();
        $this->eventManager->dispatch(
            'amasty_username_add_product_to_cart',
            ['product' => $product]
        );
        $this->messageManager->addSuccessMessage("Successfully added!");
    }
}







