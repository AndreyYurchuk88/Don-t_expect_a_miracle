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
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Model\Stock\Item;

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
     * @var Product
     */
    protected $product;

    /**
     * @var Item
     */
    protected $stockItem;

    public function __construct(
        Context $context,
        RequestInterface $request,
        RedirectFactory $resultRedirectFactory,
        ProductRepositoryInterface $productRepository,
        ManagerInterface $messageManager,
        CheckoutSession $checkoutSession,
        Product $product,
        Item $stockItem
    )
    {
        $this->context = $context;
        $this->request = $request;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->productRepository = $productRepository;
        $this->messageManager = $messageManager;
        $this->checkoutSession = $checkoutSession;
        $this->product = $product;
        $this->stockItem = $stockItem;
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
            if (!$product) {
                throw new LocalizedException(('Product not found.'));
            }

            // Проверка на simple товар
            if ($product->getTypeId() !== 'simple') {
                throw new LocalizedException(('This product is not available.'));
            }

            // Проверка qty на положительное число
            if ($qty < 1) {
                throw new LocalizedException(('Qty must be a positive number.'));
            }

            // Получаем данные о количестве товара
            $stockItem = $product->getStockItem();

            // Проверка на наличие товара в достаточном количестве
            if ($stockItem->getQty() < $qty) {
                throw new LocalizedException(('Not enough quantity available.'));
            }

            $params = [
                'product' => $product->getId(),
                'qty' => $qty,
            ];

            //добавляем в корзину
            $this->checkoutSession->getQuote()->addProduct($product, $params);
            $this->checkoutSession->getQuote()->save();

            $this->messageManager->addSuccess(('Product was successfully added to your shopping cart.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addException($e, ('Something went wrong while adding the product to cart.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
    }
}