<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Model;

use Amasty\BannerSlider\Api\Data\BannerInterface;

class Banner extends \Magento\Framework\Model\AbstractModel implements BannerInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\BannerSlider\Model\ResourceModel\Banner::class);
    }

    public function getDynamicData(): array
    {
        return [
            BannerInterface::ID => $this->getId(),
            BannerInterface::STATUS => $this->getStatus(),
            BannerInterface::IMAGE => $this->getImage(),
            BannerInterface::IMAGE_ALT => $this->getImageAlt(),
            BannerInterface::TARGET_URL => $this->getTargetUrl(),
            BannerInterface::HOVER_TEXT => $this->getHoverText(),
            BannerInterface::STORE_ID => $this->getStoreId()
        ];
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->_getData(BannerInterface::ID);
    }

    public function setId($id): BannerInterface
    {
        $this->setData(BannerInterface::ID, $id);

        return $this;
    }

    public function getName(): string
    {
        return (string) $this->_getData(BannerInterface::NAME);
    }

    public function setName(string $name): BannerInterface
    {
        $this->setData(BannerInterface::NAME, $name);

        return $this;
    }

    public function getCustomerGroup(): string
    {
        return (string) $this->_getData(BannerInterface::CUSTOMER_GROUP);
    }

    public function setCustomerGroup(string $customerGroup): BannerInterface
    {
        $this->setData(BannerInterface::CUSTOMER_GROUP, $customerGroup);

        return $this;
    }

    public function getStatus(): bool
    {
        return (bool) $this->_getData(BannerInterface::STATUS);
    }

    public function setStatus(bool $status): BannerInterface
    {
        $this->setData(BannerInterface::STATUS, $status);

        return $this;
    }

    public function getImage(): string
    {
        return (string) $this->_getData(BannerInterface::IMAGE);
    }

    public function setImage(string $Image): BannerInterface
    {
        $this->setData(BannerInterface::IMAGE, $Image);

        return $this;
    }

    public function getImageAlt(): string
    {
        return (string) $this->_getData(BannerInterface::IMAGE_ALT);
    }

    public function setImageAlt(string $imageAlt): BannerInterface
    {
        $this->setData(BannerInterface::IMAGE_ALT, $imageAlt);

        return $this;
    }

    public function getTargetUrl(): string
    {
        return (string) $this->_getData(BannerInterface::TARGET_URL);
    }

    public function setTargetUrl(string $targetUrl): BannerInterface
    {
        $this->setData(BannerInterface::TARGET_URL, $targetUrl);

        return $this;
    }

    public function getHoverText(): string
    {
        return (string) $this->_getData(BannerInterface::HOVER_TEXT);
    }

    public function setHoverText(string $hoverText): BannerInterface
    {
        $this->setData(BannerInterface::HOVER_TEXT, $hoverText);

        return $this;
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData(BannerInterface::STORE_ID);
    }

    public function setStoreId(int $storeId): BannerInterface
    {
        $this->setData(BannerInterface::STORE_ID, $storeId);

        return $this;
    }
}
