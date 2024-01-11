<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Ui\DataProvider\Form\Slider;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Api\Data\SliderInterface;
use Amasty\BannerSlider\Model\OptionSource\CustomerGroup;
use Amasty\BannerSlider\Model\Repository\SliderRepository;
use Magento\Framework\App\RequestInterface;
use Amasty\BannerSlider\Model\Slider;
use Magento\Framework\App\Request\DataPersistorInterface;
use Amasty\BannerSlider\Model\ResourceModel\Slider\CollectionFactory;
use Amasty\BannerSlider\Model\ResourceModel\Banner\CollectionFactory as BannerCollectionFactory;
use Amasty\BannerSlider\Model\ResourceModel\Banner\Collection as BannerCollection;
use Magento\Framework\Escaper;
use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var \Amasty\BannerSlider\Model\ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var SliderRepository
     */
    private $sliderRepository;

    /**
     * @var BannerCollectionFactory
     */
    private $bannerCollectionFactory;

    /**
     * @var CustomerGroup
     */
    private $customerGroup;

    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DataPersistorInterface $dataPersistor,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        PoolInterface $pool,
        RequestInterface $request,
        \Amasty\BannerSlider\Model\ImageProcessor $imageProcessor,
        SliderRepository $sliderRepository,
        BannerCollectionFactory $bannerCollectionFactory,
        CustomerGroup $customerGroup,
        Escaper $escaper,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->coreRegistry = $coreRegistry;
        $this->pool = $pool;
        $this->request = $request;
        $this->imageProcessor = $imageProcessor;
        $this->sliderRepository = $sliderRepository;
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        $this->customerGroup = $customerGroup;
        $this->escaper = $escaper;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $result = parent::getData();
        /** @var Slider $current */
        $current = $this->coreRegistry->registry(SliderInterface::PERSIST_NAME);
        if ($current->getId()) {
            $data = $this->prepareData($current->getData());
            $result[$current->getId()] = $data;
        }

        return $result;
    }

    protected function prepareData(array $data): array
    {
        if ($storeId = (int)$this->request->getParam('store', Store::DEFAULT_STORE_ID)) {
            $data['store_id'] = $storeId;
        }

        $bannerIds = $this->getExplodeData($data, Slider::BANNER_IDS);
        $positions = $this->getExplodeData($data, Slider::POSITIONS);
        $collection = $this->getBannerCollection($bannerIds);

        foreach ($collection->getData() as $index => $banner) {
            $item = $this->getBannerData($banner);
            $item[SliderInterface::POSITION] = $positions[$index];

            $data[Slider::BANNERS][Slider::BANNER_DATA][] = $item;
        }

        return $data;
    }

    private function getBannerData(array $banner): array
    {
        $item = [
            BannerInterface::ID => $banner[BannerInterface::ID],
            BannerInterface::NAME => $this->escaper->escapeHtml($banner[BannerInterface::NAME]),
            BannerInterface::STATUS => $banner[BannerInterface::STATUS],
            BannerInterface::CUSTOMER_GROUP => $this->getCustomerGroupInfo($banner)
        ];
        if (isset($banner[BannerInterface::IMAGE])) {
            $image = $banner[BannerInterface::IMAGE];
            $item[BannerInterface::IMAGE] = $this->imageProcessor->getThumbnailUrl($image);
        }

        return $item;
    }

    private function getCustomerGroupInfo(array $banner): string
    {
        $groups = explode(',', $banner[BannerInterface::CUSTOMER_GROUP]);

        return implode(', ', array_intersect_key($this->customerGroup->toArray(), $groups));
    }

    private function getExplodeData(array $data, string $field): array
    {
        if ($data[$field]) {
            $items = is_array($data[$field]) ?: explode(',', $data[$field]);
        }

        return $items ?? [];
    }

    public function getBannerCollection(array $bannerIds): BannerCollection
    {
        if (empty($bannerIds)) {
            $bannerIds = '';
        }

        $collection = $this->bannerCollectionFactory->create();
        $collection->addFieldToFilter('main_table.' . BannerInterface::ID, ['IN' => $bannerIds])
            ->addDynamicTable();

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
