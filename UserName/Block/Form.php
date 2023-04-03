<?php

namespace Amasty\UserName\Block;

use Magento\Framework\View\Element\Template;

class Form extends Template
{
    public function getCssClasses(): string
    {
        return $this->getData('css_classes') ? (string) $this->getData('css_classes') : '';
    }

    public function isQtyHidden(): bool
    {
        return $this->_scopeConfig->isSetFlag('user_config/general/hide_qty');
    }

    public function getDefaultQtyValue()
    {
        return (int) $this->_scopeConfig->getValue('user_config/general/default_qty');
    }
}
