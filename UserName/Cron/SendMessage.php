<?php

namespace Amasty\UserName\Cron;

use Magento\Framework\Mail\Template\TransportBuilder;
use Amasty\UserName\Block\Email\Email;

class SendMessage
{
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var Email
     */
    protected $email;

    public function __construct(
        TransportBuilder $transportBuilder,
        Email $email
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->email = $email;
    }

    public function execute()
    {
        $transport = $this->transportBuilder->getTransport();
        $emailBody = $transport->getMessage()->getBody();
        $this->email->setEmailBody($emailBody);
    }
}