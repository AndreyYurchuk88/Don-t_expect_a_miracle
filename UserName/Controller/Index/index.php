<?php

namespace Amasty\UserName\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\FactoryInterface;
use Amasty\UserName\Model\ResourceModel\ResourceBlacklist;

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
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var FactoryInterface
     */
    private $mailFactory;

    /**
     * @var ResourceBlacklist
     */
    private $resourceBlacklist;

    public function __construct(ResultFactory $resultFactory,
                                ScopeConfigInterface $scopeConfig,
                                TransportBuilder $transportBuilder,
                                FactoryInterface $mailFactory,
                                ResourceBlacklist $resourceBlacklist)
    {
        $this->resultFactory = $resultFactory;
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->mailFactory = $mailFactory;
        $this->resourceBlacklist = $resourceBlacklist;
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
