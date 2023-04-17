<?php

namespace Amasty\UserName\Model;

use Magento\Framework\Model\AbstractModel;

class Blacklist extends AbstractModel
{
    public function _construct()
    {
        $this->_init(\Amasty\UserName\Model\ResourceModel\ResourceBlacklist::class);
    }
}