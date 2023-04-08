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
use Amasty\UserName\Model\BlacklistFactory;

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
     * @var BlacklistFactory
     */
    protected $blacklistFactory;


    public function __construct(
        Context $context,
        RequestInterface $request,
        RedirectFactory $resultRedirectFactory,
        ProductRepositoryInterface $productRepository,
        ManagerInterface $messageManager,
        CheckoutSession $checkoutSession,
        BlacklistFactory $blacklistFactory
    )
    {
        $this->context = $context;
        $this->request = $request;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->productRepository = $productRepository;
        $this->messageManager = $messageManager;
        $this->checkoutSession = $checkoutSession;
        $this->blacklistFactory = $blacklistFactory;
    }

    public function execute()
    {
        $sku = $this->request->getParam('sku');
        $qty = (int) $this->request->getParam('qty');

        //Проверка на заполнение полей sku и qty
        if (!$sku || !$qty) {
            $this->messageManager->addError(('Please specify product and quantity.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        try {

            $product = $this->productRepository->get($sku);

            //Проверка на наличие такого товара
            if (!$product->getId()) {
                throw new LocalizedException(('Product not found.'));
            }

            // Проверка на simple товар
            if ($product->getTypeId() !== Type::TYPE_SIMPLE)  {
                throw new LocalizedException(('This product is not available.'));
            }

            // Проверка qty на положительное число
            if ($qty < 1) {
                throw new LocalizedException(('Qty must be a positive number.'));
            }

            // Проверяем, находится ли sku в Blacklist
            $blacklist = $this->blacklistFactory->create()->loadBySku($sku);
            // Если товар в Blacklist - сравниваем количество товара c qty
            if ($blacklist->getId()) {
                $qtyLimit = (int)$blacklist->getData('qty');
                $totalQty = $this->blacklistFactory->getTotalQty($sku);
                // Добавляем возможное количество
                if ($totalQty > $qtyLimit) {
                    $this->blacklistFactory->addProductToCartWithQtyLimit($product, $qtyLimit);
                    $addedMessage = __('The product was added to your cart, but the quantity was limited to %1.', $qtyLimit);
                } else {
                    $this->checkoutSession->getQuote()->addProduct($product, $qty);
                    $this->checkoutSession->getQuote()->save();
                    $addedMessage = __('The product was successfully added to your cart.');
                }
                $this->messageManager->addSuccessMessage($addedMessage);
                $totalAddedQty = $this->checkoutSession->getQuote()->getItemsQty();
                $this->messageManager->addSuccessMessage(__('Total added quantity: %1.', $totalAddedQty));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }

            // Получаем данные о количестве товара
            $productStockData = $product->getQuantityAndStockStatus();

            // Проверка на наличие товара в достаточном количестве
            if (!$productStockData['is_in_stock'] || $productStockData['qty'] < $qty) {
                throw new LocalizedException(('Not enough quantity available.'));
            }

            // Получаем квоту
            $quote = $this->checkoutSession->getQuote();

            // Проверяем наличие id и сохраняем его, если его нет
            if (!$quote->getId()) {
                $quote->save();
            }

            // Добавляем продукт и сохраняем квоту
            $params = [
                'product' => $product->getId(),
                'qty' => $qty,
            ];
            $quote->addProduct($product, $params);
            $quote->save();

            $this->messageManager->addSuccess(('Product was successfully added to your shopping cart.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, ('Something went wrong while adding the product to cart.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
    }
}