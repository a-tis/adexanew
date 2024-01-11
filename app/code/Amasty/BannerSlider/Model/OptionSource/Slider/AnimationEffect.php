<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Model\OptionSource\Slider;

use Magento\Framework\Option\ArrayInterface;

class AnimationEffect implements ArrayInterface
{
    const ROLLING = 0;

    const SPLIT = 1;

    const BUBBLE = 2;

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::ROLLING,
                'label' => __('Rolling')
            ],
            [
                'value' => self::SPLIT,
                'label' => __('Split Slideshow')
            ],
            [
                'value' => self::BUBBLE,
                'label' => __('Bubble')
            ]
        ];
    }
}
