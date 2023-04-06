<?php

namespace Amasty\SecondUsername\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Checkout\Model\Cart;

class AddPromoProductPlugin
{
    /**
     * @var RequestInterface
     */

    protected $request;

    /**
     * @var Cart
     */

    protected $cart;

    public function __construct(
        RequestInterface $request,
        Cart $cart
    ) {
        $this->request = $request;
        $this->cart = $cart;
    }

    public function aroundExecute(
        \Amasty\SecondUsername\Observer\AddPromoProduct $subject, //объект класса Amasty\SecondUsername\Observer\AddPromoProduct
        callable $proceed, //вызываем метод execute
        \Magento\Framework\Event\Observer $observer // объект-событие event
    ) {
        if (!$this->request->isAjax()) {
            return $proceed($observer);
        }
        return;
    }
}