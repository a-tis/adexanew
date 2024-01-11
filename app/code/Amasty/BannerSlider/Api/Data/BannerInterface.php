<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Api\Data;

interface BannerInterface
{
    const STATIC_TABLE_NAME = 'amasty_bannerslider_banner_static';
    const DYNAMIC_TABLE_NAME = 'amasty_bannerslider_banner_dynamic';

    const PERSIST_NAME = 'amasty_bannerslider_banner';

    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const NAME = 'name';
    const CUSTOMER_GROUP = 'customer_group';
    const STATUS = 'status';
    const IMAGE = 'image';
    const IMAGE_ALT = 'image_alt';
    const TARGET_URL = 'target_url';
    const HOVER_TEXT = 'hover_text';
    const STORE_ID = 'store_id';
    /**#@-*/

    const STATIC_FIELDS = [
        self::NAME,
        self::CUSTOMER_GROUP
    ];

    const DYNAMIC_FIELDS = [
        self::STATUS,
        self::IMAGE,
        self::IMAGE_ALT,
        self::TARGET_URL,
        self::HOVER_TEXT
    ];

    public function getId();

    public function setId($id): BannerInterface;

    public function getName(): string;

    public function setName(string $name): BannerInterface;

    public function getCustomerGroup(): string;

    public function setCustomerGroup(string $customerGroup): BannerInterface;

    public function getStatus(): bool;

    public function setStatus(bool $status): BannerInterface;

    public function getImage(): string;

    public function setImage(string $Image): BannerInterface;

    public function getImageAlt(): string;

    public function setImageAlt(string $imageAlt): BannerInterface;

    public function getTargetUrl(): string;

    public function setTargetUrl(string $targetUrl): BannerInterface;

    public function getHoverText(): string;

    public function setHoverText(string $hoverText): BannerInterface;

    public function getStoreId(): int;

    public function setStoreId(int $storeId): BannerInterface;
}
