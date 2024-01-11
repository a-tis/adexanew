<?php
namespace Keepcalmlabs\HomePage\Block;


use Magento\Framework\View\Element\Template;
use Magento\Catalog\Helper\ImageFactory as HelperFactory;
use Magento\Framework\View\Asset\Repository;
use Magento\Catalog\Block\Product\ListProduct;


class BestSellerProduct extends Template
{

    protected $_collectionFactory;

    protected $_productsFactory;
    protected $_storeManager;

    /**
     * @var HelperFactory
     */
    protected $helperFactory;
    /**
     * @var Repository
     */
    protected $assetRepos;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,

        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory,

        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory,

        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,

        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productrepository,
        HelperFactory $helperFactory,
        Repository $repository,
        array $data = []
    ) {
        $this->_productsFactory = $productsFactory;
        $this->_storeManager = $storeManager;
        $this->productrepository = $productrepository;

        $this->_productCollectionFactory = $productCollectionFactory;

        $this->_collectionFactory = $collectionFactory;

        $this->helperFactory = $helperFactory;
        $this->assetRepos = $repository;

        $this->listProductBlock = $listProductBlock;
        parent::__construct($context, $data);
    }


    public function getBestSellerData(){

        $bestSeller = $this->_collectionFactory->create()
            ->setModel('Magento\Catalog\Model\Product')
            ->setPeriod('month') //you can add period daily,yearly
        ;


        $bestSellerId = [];
        foreach ($bestSeller as $product) {
            $bestSellerId[] = $product->getProductId();
        }
        $deleteDublicates = array_unique($bestSellerId);

        $arraySum = [];

        foreach ($deleteDublicates as $withOutDublicates) {
            $sum =  0;
            foreach ($bestSeller as $product) {
                if($withOutDublicates == $product->getProductId()){
                    $sum+= $product->getQtyOrdered();
                    $arraySum[$withOutDublicates] = $sum;
                }
            }
        }

        $bestSellerIdForCollection = [];
        foreach ($arraySum as $key=>$values){
            $bestSellerIdForCollection[] = $key;
        }


        return $bestSellerIdForCollection;
    }



    public function getBestSellerCollection() {
        $idForCollection = $this->getBestSellerData();
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('entity_id',array('in'=> $idForCollection ));
        $collection->setFlag('has_stock_status_filter', false);
        $collection->joinField('stock_item', 'cataloginventory_stock_item', 'is_in_stock', 'product_id=entity_id', 'is_in_stock=1');
        $collection->addWebsiteFilter();
        $collection->addStoreFilter();
        $collection->setPageSize(7);


        return $collection;
    }

    public function getProductCollectionLastAdded() {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToSort('entity_id','desc');
        $collection->setFlag('has_stock_status_filter', false);
        $collection->joinField('stock_item', 'cataloginventory_stock_item', 'is_in_stock', 'product_id=entity_id', 'is_in_stock=1');
        $collection->addWebsiteFilter();


        // filter current store products
        $collection->addStoreFilter();
        $collection->setPageSize(7);


        return $collection;
    }
    /**
     * Get Product Collection of MostViewed Products
     * @return mixed
     */
    public function getProductCollection()
    {
        $currentStoreId = $this->_storeManager->getStore()->getId();

        $collection = $this->_productsFactory->create()
            ->addAttributeToSelect('*')
             ->setFlag('has_stock_status_filter', false)
             ->joinField('stock_item', 'cataloginventory_stock_item', 'is_in_stock', 'product_id=entity_id', 'is_in_stock=1')
            ->addViewsCount()
            ->setStoreId($currentStoreId)
            ->addStoreFilter($currentStoreId)
            ->setPageSize(7);
        return $collection;
    }
    Public function getProductImageUsingCode($productId)
    {
        $store = $this->_storeManager->getStore();

        $product = $this->productrepository->getById($productId);

        $imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
        if(!$product->getImage()){
            $imageUrl = $this->getPlaceHolderImage();
        }
        return $imageUrl;
    }
    /**
     * Get small place holder image
     *
     * @return string
     */
    public function getPlaceHolderImage()
    {
        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->helperFactory->create();
        return $this->assetRepos->getUrl($helper->getPlaceholder('small_image'));
    }
    public function getAddToCartPostParams($product)
    {
        return $this->listProductBlock->getAddToCartUrl($product);
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