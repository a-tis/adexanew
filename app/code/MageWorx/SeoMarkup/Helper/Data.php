<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SeoMarkup\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * SEO markup data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_COMMON_TWITTER_USERNAME = 'mageworx_seo/markup/common/tw_username';

    /**
     * Retrieve twitter username
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCommonTwUsername($storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_COMMON_TWITTER_USERNAME,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
