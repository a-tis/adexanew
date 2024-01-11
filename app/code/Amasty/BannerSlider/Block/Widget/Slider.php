<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Block\Widget;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Api\Data\SliderInterface;
use Amasty\BannerSlider\Api\SliderRepositoryInterface;
use Amasty\BannerSlider\Model\Repository\BannerRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Slider extends Template implements BlockInterface
{
    protected $_template = 'Amasty_BannerSlider::widget/slider.phtml';

    /**
     * @var SliderRepositoryInterface
     */
    private $sliderRepository;

    /**
     * @var \Amasty\BannerSlider\Model\ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var Template\Context
     */
    private $context;

    /**
     * @var BannerRepository
     */
    private $bannerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        \Amasty\BannerSlider\Model\ImageProcessor $imageProcessor,
        SliderRepositoryInterface $sliderRepository,
        Template\Context $context,
        BannerRepository $bannerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sliderRepository = $sliderRepository;
        $this->imageProcessor = $imageProcessor;
        $this->context = $context;
        $this->bannerRepository = $bannerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return str_replace('\\', '-', $this->getNameInLayout());
    }

    /**
     * @return SliderInterface|null
     */
    public function getSlider(): ?SliderInterface
    {
        $slider = $this->getData('slider');
        if (!is_object($slider)) {
            try {
                $storeId = (int) $this->context->getStoreManager()->getStore()->getId();
                $slider = $this->sliderRepository->getById((int) $this->getData('slider_id'), $storeId);
                if (!$this->validateSlider($slider)) {
                    $slider = null;
                }
            } catch (NoSuchEntityException $e) {
                $slider = null;
            }
            $this->setData('slider', $slider);
        }

        return $slider;
    }

    private function validateSlider(SliderInterface $slider): bool
    {
        return (bool) $slider->getStatus();
    }

    public function getBanners()
    {
        $bannersList = $this->getData('banners');
        if (!is_array($bannersList)) {
            $bannersList = $this->getBannerList();
            $this->setData('banners', $bannersList);
        }

        return $bannersList;
    }

    private function getBannerList()
    {
        $this->searchCriteriaBuilder->addFilter(
            BannerInterface::ID,
            $this->getSlider()->getBannerIds() ?: '',
            'in'
        );

        return $this->bannerRepository->getValidList($this->searchCriteriaBuilder->create());
    }

    public function getImageSrc(BannerInterface $banner): string
    {
        /** @var SliderInterface $slider */
        $slider = $this->getData('slider');

        if ($slider->getResizeImages()) {
            $imageSrc = $this->imageProcessor->getResizedUrl(
                $banner->getImage(),
                $slider->getBannerWidth(),
                $slider->getBannerHeight()
            );
        } else {
            $imageSrc = $this->imageProcessor->getThumbnailUrl($banner->getImage());
        }

        return $imageSrc;
    }

    public function getSliderClass(): string
    {
        $slider = $this->getSlider();

        return sprintf(
            '%s%s%s%s',
            $slider->getNavigationArrows() ? '-ambanner-arrows ' : '',
            $slider->getNavigationBullets() ? '-ambanner-dots ' : '',
            ' -arrows-' . $slider->getArrowsStyle(),
            ' -dots-' . $slider->getBulletsStyle()
        );
    }
}
