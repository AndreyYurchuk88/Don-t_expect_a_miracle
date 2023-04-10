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

    public function sendEmail()
    {
        $sku = '24-MB03';
        $qty = $this->resourceBlacklist->getQtyBySku($sku);

        $templateId = 'amasty_username_blacklist_email_template';
        $templateVars = [
            'qty' => $qty,
        ];
        $templateOptions = [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => 0,
        ];
        $sender = [
            'name' => 'Sender Name',
            'email' => 'sender@example.com'
        ];
        $recipient = [
            'name' => 'Recipient Name',
            'email' => 'recipient@example.com'
        ];

        $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($sender)
            ->addTo($recipient)
            ->getTransport();

        $transport->sendMessage();
    }
}
