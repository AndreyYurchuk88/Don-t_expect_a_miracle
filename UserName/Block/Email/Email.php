<?php

namespace Amasty\UserName\Block\Email;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Amasty\UserName\Model\ResourceModel\ResourceBlacklist;

class Email extends Template
{
    /**
     * @var ResourceBlacklist
     */
    protected $resourceBlacklist;

    public function __construct(
        Context $context,
        ResourceBlacklist $resourceBlacklist,
        array $data = []
    ) {
        $this->resourceBlacklist = $resourceBlacklist;
        parent::__construct($context, $data);
    }

    public function getQty()
    {
        $sku = '24-MB03'; //sku из письма
        $blacklistItem = $this->resourceBlacklist->getBySku($sku);
        return $blacklistItem->getQty();
    }

    public function setEmailBody($body)
    {
        $blacklistItem = $this->resourceBlacklist->getBySku('24-MB03'); //sku из письма
        $blacklistItem->setEmailBody($body);
        $blacklistItem->save();
    }
}

