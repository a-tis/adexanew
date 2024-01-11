<?php
namespace Jofab\Pricing\Plugin\Model\Directory;

class PriceCurrency
{
    public function aroundFormat(
        \Magento\Directory\Model\PriceCurrency $subject,
        callable $proceed,
        $amount,
        $includeContainer = true,
        $precision = \Magento\Directory\Model\PriceCurrency::DEFAULT_PRECISION,
        $scope = null,
        $currency = null
    ) {
        return $proceed($amount, $includeContainer, 0, $scope, $currency);
    }
}