<?php

namespace Amasty\UserName\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;

class Index implements ActionInterface
{
    /**
     * @var ResultFactory
     */

    private $resultFactory;

    /**
     * @var ScopeConfigInterface
     */

    private $scopeConfig;

    public function __construct(ResultFactory $resultFactory,
                                ScopeConfigInterface $scopeConfig)
    {
        $this->resultFactory = $resultFactory;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute()
    {
        if ($this->scopeConfig->isSetFlag('user_config/general/enabled')) {
            return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        } else {
            die('Sorry, the page cannot be loaded...');
        }
    }
}
