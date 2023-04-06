<?php

namespace Amasty\SecondUsername\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\RequestInterface;

class AddPromoProduct implements ObserverInterface
{
    /**
     * @var Cart
     */

    protected $cart;

    /**
     * @var RequestInterface
     */

    protected $request;

    public function __construct(
        Cart $cart,
        RequestInterface $request
    ) {
        $this->cart = $cart;
        $this->request = $request;
    }

    public function execute(EventObserver $observer)
    {
        $product = $observer->getEvent()->getData('product');
        $forSku = '';
        $promoSku = '';

        if (strpos($forSku, $product->getSku()) !== false) {
            $params = array(
                'product' => $promoSku,
                'qty' => 1
            );
            $this->cart->addProduct($promoSku, $params);
            $this->cart->save();
        }
    }
}