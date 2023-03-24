<?php

namespace Amasty\UserName\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

abstract class ConfigProviderAbstract
{
    protected $scopeConfig;

    protected $pathPrefix;

    public function __construct(ScopeConfigInterface $scopeConfig,
                                                     $pathPrefix = '')
    {
        $this->scopeConfig = $scopeConfig;
        $this->pathPrefix = $pathPrefix;
    }

    //метод getValue() достаёт значения из scopeConfig только для pathPrefix
    protected function getValue($path, $scope = 'store', $storeId = null)
    {
        $path = $this->pathPrefix . $path;
        return $this->scopeConfig->getValue($path, $scope, $storeId);
    }
}
