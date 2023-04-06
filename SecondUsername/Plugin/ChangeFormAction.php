<?php

namespace Amasty\SecondUsername\Plugin;

use Magento\Framework\App\RequestInterface;

class ChangeFormAction
{
    /**
     * @var RequestInterface
     */

    protected $request;

    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    public function aroundGetFormAction(
        \Amasty\UserName\Block\Form $subject, //объект класса Amasty\UserName\Block\Form
        callable $proceed //вызываем метод getFormAction
    ) {
        $params = ['sku' => $this->request->getParam('sku')]; //получаем параметр sku
        $url = 'checkout/cart/add/sku/' . $params['sku']; //формируем url контроллера Magento + value sku
        return $subject->getUrl($url);
    }
}