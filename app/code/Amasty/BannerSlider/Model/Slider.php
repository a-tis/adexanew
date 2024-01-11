<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Model;

use Amasty\BannerSlider\Api\Data\SliderInterface;

class Slider extends \Magento\Framework\Model\AbstractModel implements SliderInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\BannerSlider\Model\ResourceModel\Slider::class);
    }

    public function getDynamicData(): array
    {
        return [
            SliderInterface::ID => $this->getId(),
            SliderInterface::NAME => $this->getName(),
            SliderInterface::STATUS => $this->getStatus(),
            SliderInterface::STORE_ID => $this->getStoreId()
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAutoplay()
    {
        return $this->_getData(SliderInterface::AUTOPLAY);
    }

    /**
     * @inheritdoc
     */
    public function setAutoplay($autoplay)
    {
        $this->setData(SliderInterface::AUTOPLAY, $autoplay);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPauseTime()
    {
        return $this->_getData(SliderInterface::PAUSE_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setPauseTime($pauseTime)
    {
        $this->setData(SliderInterface::PAUSE_TIME, $pauseTime);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAnimationEffect()
    {
        return $this->_getData(SliderInterface::ANIMATION_EFFECT);
    }

    /**
     * @inheritdoc
     */
    public function setAnimationEffect($animationEffect)
    {
        $this->setData(SliderInterface::ANIMATION_EFFECT, $animationEffect);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTransitionSpeed()
    {
        return $this->_getData(SliderInterface::TRANSITION_SPEED);
    }

    /**
     * @inheritdoc
     */
    public function setTransitionSpeed($transitionSpeed)
    {
        $this->setData(SliderInterface::TRANSITION_SPEED, $transitionSpeed);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNavigationArrows()
    {
        return $this->_getData(SliderInterface::NAVIGATION_ARROWS);
    }

    /**
     * @inheritdoc
     */
    public function setNavigationArrows($navigationArrows)
    {
        $this->setData(SliderInterface::NAVIGATION_ARROWS, $navigationArrows);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArrowsStyle()
    {
        return $this->_getData(SliderInterface::ARROWS_STYLE);
    }

    /**
     * @inheritdoc
     */
    public function setArrowsStyle($arrowsStyle)
    {
        $this->setData(SliderInterface::ARROWS_STYLE, $arrowsStyle);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNavigationBullets()
    {
        return $this->_getData(SliderInterface::NAVIGATION_BULLETS);
    }

    /**
     * @inheritdoc
     */
    public function setNavigationBullets($navigationBullets)
    {
        $this->setData(SliderInterface::NAVIGATION_BULLETS, $navigationBullets);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBulletsStyle()
    {
        return $this->_getData(SliderInterface::BULLETS_STYLE);
    }

    /**
     * @inheritdoc
     */
    public function setBulletsStyle($bulletsStyle)
    {
        $this->setData(SliderInterface::BULLETS_STYLE, $bulletsStyle);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBannerWidth()
    {
        return $this->_getData(SliderInterface::BANNER_WIDTH);
    }

    /**
     * @inheritdoc
     */
    public function setBannerWidth($bannerWidth)
    {
        $this->setData(SliderInterface::BANNER_WIDTH, $bannerWidth);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBannerHeight()
    {
        return $this->_getData(SliderInterface::BANNER_HEIGHT);
    }

    /**
     * @inheritdoc
     */
    public function setBannerHeight($bannerHeight)
    {
        $this->setData(SliderInterface::BANNER_HEIGHT, $bannerHeight);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getResizeImages()
    {
        return $this->_getData(SliderInterface::RESIZE_IMAGES);
    }

    /**
     * @inheritdoc
     */
    public function setResizeImages($resizeImages)
    {
        $this->setData(SliderInterface::RESIZE_IMAGES, $resizeImages);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->_getData(SliderInterface::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        $this->setData(SliderInterface::STORE_ID, $storeId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_getData(SliderInterface::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->setData(SliderInterface::NAME, $name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->_getData(SliderInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(SliderInterface::STATUS, $status);

        return $this;
    }
}
