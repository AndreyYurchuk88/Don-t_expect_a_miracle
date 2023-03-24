<?php

namespace Amasty\UserName\Block;

use Magento\Framework\View\Element\Template;

class Form extends Template
{
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
}
