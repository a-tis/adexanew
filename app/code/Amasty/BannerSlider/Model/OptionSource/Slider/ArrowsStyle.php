<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Model\OptionSource\Slider;

use Magento\Framework\Option\ArrayInterface;

class ArrowsStyle implements ArrayInterface
{
    const FIRST = 1;

    const SECOND = 2;

    const THIRD = 3;

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::FIRST,
                'label' => __('Arrow #1')
            ],
            [
                'value' => self::SECOND,
                'label' => __('Arrow #2')
            ],
            [
                'value' => self::THIRD,
                'label' => __('Arrow #3')
            ]
        ];
    }
}
