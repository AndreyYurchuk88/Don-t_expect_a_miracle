<?php

namespace Amasty\UserName\Model\Config;

use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends ConfigProviderAbstract
{
    const PATH_PREFIX = 'user_config/';

    //получаем значение настроек enabled и welcome_text из scopeConfig

    public function isEnabled($storeId = null)
    {
        return (bool) $this->getValue('general/enabled', ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getWelcomeText($storeId = null)
    {
        return $this->getValue('general/welcome_text', ScopeInterface::SCOPE_STORE, $storeId);
    }
}
