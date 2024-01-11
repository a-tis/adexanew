<?php

namespace Webkul\Grid\Block;

use Magento\Framework\View\Element\Template;
use Webkul\Grid\Model\ResourceModel\Grid\Collection;
use Webkul\Grid\Model\ResourceModel\Grid\CollectionFactory;

class Hello extends Template
{
    private $collectionFactory;
    private $filterProvider;
    protected $_storeManager;

    protected $_productCollectionFactory;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productrepository,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filterProvider = $filterProvider;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->productrepository = $productrepository;
        parent::__construct($context, $data);
    }

    /**
     * @return \Mastering\SampleModule\Model\Item[]
     */
    public function getItems()
    {
        return $this->collectionFactory->create()->getItems();
    }

    public function getImage()
    {
        return $this->getItems();
    }

    public function getStoreCurrenccy()
    {
        return $this->_storeManager->getStore();
    }



    public function getProductDataUsingSku($idForCollection) {
        return $this->productrepository->get($idForCollection);
    }
    public function getProductImageUsingCode($productId)
    {
        $store = $this->_storeManager->getStore();

        $product = $this->productrepository->getById($productId);

        $imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
        if(!$product->getImage()){
            $imageUrl = $this->getPlaceHolderImage();
        }
        return $imageUrl;
    }

    public function getPrice(\Magento\Catalog\Model\Product $product)
    {
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->getLayout()->createBlock(
                \Magento\Framework\Pricing\Render::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'display_minimal_price'  => true,
                    'use_link_for_as_low_as' => true,
                    'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST
                ]
            );
        }

        return $price;
    }
}