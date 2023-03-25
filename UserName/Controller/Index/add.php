<?php

namespace Amasty\UserName\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Validator\GreaterThan;

class AddToCart implements ActionInterface
{
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var ProductRepositoryInterface
     */

    protected $productRepository;

    /**
     * @var GreaterThan
     */
    protected $greaterThanValidator;

    /**
     * @var ManagerInterface
     */

    protected $messageManager;

    /**
     * @var as CheckoutSession
     */

    protected $checkoutSession;


    public function construct(Context $context,
                              RedirectFactory $resultRedirectFactory,
                              ProductRepositoryInterface $productRepository,
                              GreaterThan $greaterThanValidator,
                              ManagerInterface $messageManager,
                              CheckoutSession $checkoutSession)
    {
        parent::__construct($context);
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->productRepository = $productRepository;
        $this->greaterThanValidator = $greaterThanValidator;
        $this->messageManager = $messageManager;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute()
    {
        $sku = $this->getRequest()->getParam('sku');
        $qty = (int) $this->getRequest()->getParam('qty');
        $minValue = 1;

        if (!$this->getRequest()->isPost() || !$sku || !$qty) {
            $this->messageManager->addError(('Please specify product and quantity.'));
            return $this->resultRedirectFactory->create()->setPath('username/index/add');
        }

        try {
            $product = $this->productRepository->get($sku);
            if ($product->getTypeId() !== 'simple') {
                throw new LocalizedException(('This product is not available.'));
            }
            if (!$this->greaterThanValidator->isValid($qty, $minValue)) {
                throw new LocalizedException(('Qty must be a positive number.'));
            }

            $params = [
                'product' => $product->getId(),
                'qty' => $qty,
            ];

            //добавляем в корзину
            $this->checkoutSession->getQuote()->addProduct($product, $params);
            $this->checkoutSession->getQuote()->save();

            $this->messageManager->addSuccess(('Product was successfully added to your shopping cart.'));
            return $this->resultRedirectFactory->create()->setPath('username/index/add');
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath('username/index/add');
        } catch (\Exception $e) {
            $this->messageManager->addException($e, ('Something went wrong while adding the product to cart.'));
            return $this->resultRedirectFactory->create()->setPath('username/index/add');
        }
    }
}