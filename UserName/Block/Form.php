<?php

namespace Amasty\UserName\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Event\ManagerInterface as EventManager;

class Form extends Template
{
    const FORM_ACTION = 'username/index/addtocart';

    /**
     * @var EventManager
     */

    protected $eventManager;

    public function __construct(
        Template\Context $context,
        EventManager $eventManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->eventManager = $eventManager;
    }

    public function addProductToCart($product)  //добавляем товар в корзину
    {
        $this->eventManager->dispatch('amasty_username_add_product_to_cart', ['product' => $product]);
    }

    public function getCssClasses()
    {
        return $this->getData('css_classes') ? (string) $this->getData('css_classes') : '';
    }

    public function isQtyHidden()
    {
        return (bool) $this->_scopeConfig->getValue('user_config/general/hide_qty');
    }

    public function getDefaultQtyValue()
    {
        return (int) $this->_scopeConfig->getValue('user_config/general/default_qty');
    }

    public function getFormAction()
    {
        return self::FORM_ACTION;
    }
}

