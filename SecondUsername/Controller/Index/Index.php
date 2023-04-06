<?php

namespace Amasty\SecondUsername\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session;

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

    /**
     * @var Session
     */
    private $customerSession;

    public function __construct(
        ResultFactory $resultFactory,
        ScopeConfigInterface $scopeConfig,
        Session $customerSession
    ) {
        $this->resultFactory = $resultFactory;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
    }

    public function execute()
    {
        if ($this->customerSession->isLoggedIn() && $this->scopeConfig->isSetFlag('user_config/general/enabled')) {
            return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        } else {
            die('Sorry, the page cannot be loaded...');
        }
    }
}