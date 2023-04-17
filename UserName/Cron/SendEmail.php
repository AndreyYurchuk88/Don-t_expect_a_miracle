<?php

namespace Amasty\UserName\Cron;

use Psr\Log\LoggerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\Factory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Amasty\UserName\Model\BlacklistRepository;

class SendEmail
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var Factory
     */
    private $templateFactory;

    /**
     * @var BlacklistRepository
     */
    private $blackListRepository;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        LoggerInterface $logger,
        TransportBuilder $transportBuilder,
        Factory $templateFactory,
        BlacklistRepository $blackListRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->logger = $logger;
        $this->transportBuilder = $transportBuilder;
        $this->templateFactory = $templateFactory;
        $this->blackListRepository = $blackListRepository;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute()
    {
        $blackListItem = $this->blackListRepository->getBySku('24-MB03');

        $templateId = $this->scopeConfig->getValue('user_config/cron/email_template');

        $templateVars = [
            'qty' => $blackListItem->getQty()
        ];

        $templateOptions = [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => 0
        ];

        $template = $this->templateFactory->get($templateId);
        $template->setVars($templateVars)
            ->setOptions($templateOptions);

        $emailBody = $template->processTemplate();
        $this->blackListRepository->addEmailBody($blackListItem, $emailBody);

        $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions($templateOptions)
            ->setFrom([
                'name' => 'Sender Name',
                'email' => 'sender@example.com'
            ])
            ->addTo([
                'name' => 'Recipient Name',
                'email' => 'recipient@example.com'
            ])
            ->getTransport();

        $transport->sendMessage();

        $this->logger->debug('Amasty_UserName Module job done');
    }
}