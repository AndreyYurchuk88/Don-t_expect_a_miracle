<?php

namespace Amasty\UserName\Block;

use Magento\Framework\View\Element\Template;

class Form extends Template
{
    public function getCssClasses() {
        return $this->getData('css_classes');
    }
}