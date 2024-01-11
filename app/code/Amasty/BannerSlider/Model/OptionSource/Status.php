<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Model\OptionSource;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    const DISABLED = 0;

    const ENABLED = 1;

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::ENABLED,
                'label' => __('Enable')
            ],
            [
                'value' => self::DISABLED,
                'label' => __('Disable')
            ]
        ];
    }

    public function toArray(): array
    {
        return [
            self::ENABLED => __('Enable'),
            self::DISABLED => __('Disable')
        ];
    }
}
