<?php

namespace Amasty\UserName\Model;

use Magento\Framework\Model\AbstractModel;

class Blacklist extends AbstractModel
{
    public function _construct()
    {
        this->_init(\Amasty\UserName\Model\ResourceModel\ResourceBlacklist::class);
    }

    public function getTotalQty($sku)   //сравниваем количество товара
    {
        $product = $this->productRepository->get($sku); //получаем объект товара по sku
        $cartQty = $this->cart->getQuote()->getItemQty($product->getId()); //количество единиц товара в корзине
        $totalQty = $cartQty + $product->getQty(); //сумма товаров
        return $totalQty;
    }

    public function addProductToCartWithQtyLimit($product, $qtyLimit) //добавление возможного количества товара
    {
        $totalQty = $this->getTotalQty($product->getSku()); //общее количество товаров в корзине
        $qtyToAdd = $qtyLimit - $totalQty; // количество, которое можно добавить в корзину; $qtyLimit - кол-во товаров в таблице
        if ($qtyToAdd > 0) {
            $this->addProductToCart($product, $qtyToAdd);
            $message = __('Only %1 item(s) can be added to the cart.', $qtyToAdd);
            $this->messageManager->addError($message);
        }
    }
}