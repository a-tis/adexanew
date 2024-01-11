<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionSkuPolicy\Plugin;

use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Quote\Model\QuoteManagement;
use MageWorx\OptionSkuPolicy\Helper\Data as Helper;
use Magento\Checkout\Model\Cart;

class AddSkuPolicyToOrder
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var ProductHelper
     */
    protected $productHelper;

    /**
     * @param ProductHelper $productHelper
     * @param Helper $helper
     * @param Cart $cart
     */
    public function __construct(
        Helper $helper,
        ProductHelper $productHelper,
        Cart $cart
    ) {
        $this->productHelper = $productHelper;
        $this->helper        = $helper;
        $this->cart          = $cart;
    }

    /**
     * Allow to apply SKU Policy to quote before order submit
     *
     * @param QuoteManagement $subject
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $orderData
     * @return \Magento\Framework\Model\AbstractExtensibleModel|\Magento\Sales\Api\Data\OrderInterface|object|null
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSubmit($subject, $quote, $orderData = [])
    {
        if ($quote->getAllVisibleItems() && !$this->helper->isSkuPolicyAppliedToCartAndOrder()) {
            $quote->setCanApplySkuPolicyToOrder(true);
            $quote->setTotalsCollectedFlag(false);
            $quote->setIsSuperMode(true);
            $this->productHelper->setSkipSaleableCheck(true);
            $this->cart->save();
        }

        return [$quote, $orderData];
    }
}
