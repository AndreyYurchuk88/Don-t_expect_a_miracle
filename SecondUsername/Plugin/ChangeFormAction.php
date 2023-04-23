<?php

namespace Amasty\SecondUsername\Plugin;

use Magento\Framework\View\Element\AbstractBlock;

class ChangeFormAction extends AbstractBlock
{
    public function afterGetFormAction(
        \Amasty\UserName\Block\Form $subject, //объект класса Amasty\UserName\Block\Form
                                    $result //вызов оригинального метода
    ) {
        return $this->getUrl('checkout/cart/add/sku');
    }
}