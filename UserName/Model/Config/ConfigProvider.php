<?php

namespace Amasty\UserName\Model\Config;

use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends ConfigProviderAbstract
{
    protected $pathPrefix = 'user_config/';
    const PATH_ENABLED = 'general/enabled';
    const PATH_WELCOME_TEXT = 'general/welcome_text';

    //получаем значение настроек enabled и welcome_text из scopeConfig
    public function isEnabled(?int $storeId = null): bool
    {
        return (bool) $this->getValue(self::PATH_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getWelcomeText(?int $storeId = null): string
    {
        return $this->getValue(self::PATH_WELCOME_TEXT, ScopeInterface::SCOPE_STORE, $storeId);
    }

    //метод для значения конфига enabled
    public function isQtyFieldHidden(?int $storeId = null): bool
    {
        return $this->isEnabled($storeId) && $this->getValue('general/hide_qty', ScopeInterface::SCOPE_STORE, $storeId);
    }
}
