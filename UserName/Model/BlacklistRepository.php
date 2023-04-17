<?php

namespace Amasty\UserName\Model;

use Amasty\UserName\Model\BlacklistFactory;
use Amasty\UserName\Model\ResourceModel\ResourceBlacklist;

class BlacklistRepository
{
    /**
     * @var ResourceBlacklist
     */
    private $resourceBlacklist;

    /**
     * @var BlacklistFactory
     */
    private $blacklistFactory;

    public function __construct(
        ResourceBlacklist $resourceBlacklist,
        BlacklistFactory $blacklistFactory
    )
    {
        $this->resourceBlacklist = $resourceBlacklist;
        $this->blacklistFactory = $blacklistFactory;
    }

    public function getBySku($sku)
    {
        $blacklistSku = $this->blacklistFactory->create();
        $this->resourceBlacklist->load($blacklistSku, $sku, 'sku');
        return $blacklistSku;
    }

    public function addEmailBody($blackListItem, $emailBody)
    {
        $blackListItem->setEmailBody($emailBody);
        $this->resourceBlacklist->save($blackListItem);
    }
}