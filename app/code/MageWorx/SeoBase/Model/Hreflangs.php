<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SeoBase\Model;

use MageWorx\SeoBase\Helper\Data as HelperData;
use MageWorx\SeoBase\Helper\Url as HelperUrl;

abstract class Hreflangs implements \MageWorx\SeoBase\Model\HreflangsInterface
{
    /**
     * @var \MageWorx\SeoBase\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\SeoBase\Helper\Url
     */
    protected $helperUrl;

    /**
     * @var string
     */
    protected $fullActionName;

    /**
     * @var \Magento\Framework\Model\AbstractModel|null
     */
    protected $entity;

    /**
     * Retrieve hreflang URL list:
     * [
     *      (int)$storeId => (string)$hreflangUrl,
     *      ...
     * ]
     *
     * @return array
     */
    abstract public function getHreflangUrls();

    /**
     *
     * @param HelperData $helperData
     * @param HelperUrl $helperUrl
     * @param string $fullActionName
     */
    public function __construct(
        HelperData $helperData,
        HelperUrl $helperUrl,
        $fullActionName
    ) {
        $this->helperData     = $helperData;
        $this->helperUrl      = $helperUrl;
        $this->fullActionName = $fullActionName;
    }

    /**
     * Check if cancel adding hreflangs URL by config setting
     *
     * @return bool
     */
    protected function isCancelHreflangs(): bool
    {
        return ($this->helperData->isHreflangsEnabled() == false);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $entity
     * @return $this
     */
    public function setEntity(\Magento\Framework\Model\AbstractModel $entity): Hreflangs
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel|null
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
