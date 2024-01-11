<?php
namespace Jofab\Pricing\Plugin\Framework\Locale;

class Format
{
    public function afterGetPriceFormat(
        \Magento\Framework\Locale\Format $subject,
        $result
    ){
        $result['precision'] = $result['requiredPrecision'] = 0;
        $result['groupSymbol'] = '';
        return $result;
    }
}