<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model\ResourceModel\Catalog;

use Exception;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\XmlSitemap\Helper\Data;
use Zend_Db_Statement_Exception;

/**
 * {@inheritdoc}
 */
class Category extends \Magento\Sitemap\Model\ResourceModel\Catalog\Category
{
    /**
     * @var Collection
     */
    protected $query;

    /**
     * @var bool
     */
    protected $readed = false;

    /**
     * @var Data
     */
    protected $helperSitemap;

    /**
     * @var bool|null
     */
    protected $usePubInMediaUrls;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Category constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Category $categoryResource
     * @param MetadataPool $metadataPool
     * @param Data $helperData
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category $categoryResource,
        MetadataPool $metadataPool,
        Data $helperData,
        ManagerInterface $eventManager,
        CollectionFactory $collectionFactory,
        string $connectionName = null
    ) {
        $this->helperSitemap     = $helperData;
        $this->eventManager      = $eventManager;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context, $storeManager, $categoryResource, $metadataPool, $connectionName);
    }

    /**
     * Get category collection array
     * Call this function while !isCollectionReaded() to read all collection
     *
     * @param null|string|bool|int|Store $storeId
     * @param int $limit
     * @param null $usePubInMediaUrls
     * @return DataObject[]|bool
     * @throws LocalizedException
     * @throws Zend_Db_Statement_Exception
     */
    public function getLimitedCollection($storeId, $limit, $usePubInMediaUrls = null)
    {
        $this->usePubInMediaUrls = $usePubInMediaUrls;

        $categories = [];

        /* @var $store Store */
        $store = $this->_storeManager->getStore($storeId);
        if (!$store) {
            return false;
        }

        if ($limit <= 0) {
            return false;
        }

        if (!isset($this->query)) {
            $connect       = $this->getConnection();
            $this->_select = $connect->select()->from(
                $this->getMainTable()
            )->where(
                $this->getIdFieldName() . '=?',
                $store->getRootCategoryId()
            );
            $categoryRow   = $connect->fetchRow($this->_select);

            if (!$categoryRow) {
                return false;
            }

            $this->_select = $connect->select()->from(
                ['e' => $this->getMainTable()],
                [$this->getIdFieldName(), 'updated_at']
            )->joinLeft(
                ['url_rewrite' => $this->getTable('url_rewrite')],
                'e.entity_id = url_rewrite.entity_id AND url_rewrite.is_autogenerated = 1'
                . $connect->quoteInto(' AND url_rewrite.store_id = ?', $store->getId())
                . $connect->quoteInto(' AND url_rewrite.entity_type = ?', CategoryUrlRewriteGenerator::ENTITY_TYPE),
                ['url' => 'request_path']
            )->where(
                'e.path LIKE ?',
                $categoryRow['path'] . '/%'
            );

            if ($this->helperSitemap->isCategoryImages()) {
                $this->_joinAttribute($storeId, 'image', 'image');
                $this->_joinAttribute($storeId, 'name', 'name');
            }

            $this->_addFilter($storeId, 'is_active', 1);
            $this->_addFilter($storeId, 'in_xml_sitemap', 1);

            if ($this->_categoryResource->getAttribute('meta_robots')) {
                $metaRobotsExclusionList = $this->helperSitemap->getMetaRobotsExclusion();

                if ($metaRobotsExclusionList) {
                    $this->_addExtendedFilter($store->getId(), 'meta_robots', $metaRobotsExclusionList, 'nin');
                }
            }

            $this->eventManager->dispatch(
                'mageworx_xmlsitemap_category_generation_before',
                ['select' => $this->_select, 'store_id' => $storeId]
            );

            $this->query  = $connect->query($this->_select);
            $this->readed = false;
        }

        for ($i = 0; $i < $limit; $i++) {
            if (!$row = $this->query->fetch()) {
                $this->readed = true;
                break;
            }

            $category                       = $this->_prepareCategory($row);
            $categories[$category->getId()] = $category;
        }

        return $categories;
    }

    /**
     * Prepare category
     *
     * @param array $categoryRow
     * @return DataObject
     * @throws Exception
     */
    protected function _prepareCategory(array $categoryRow)
    {
        $category = parent::_prepareCategory($categoryRow);

        if (!empty($categoryRow['image']) && !empty($categoryRow['name'])) {


            $imageUrl = $this->_storeManager->getStore()->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                ) . 'catalog/category/' . $categoryRow['image'];

            $imageUrl = $this->cropPubFromImageUrl($imageUrl);

            $imagesCollection = [];

            $image = new DataObject();
            $image->setUrl($imageUrl);
            $image->setThumbnail($imageUrl);
            $image->setTitle($categoryRow['name']);

            $imagesCollection[] = $image;

            $category->setImages(
                new DataObject(
                    ['collection' => $imagesCollection, 'title' => $categoryRow['name'], 'thumbnail' => $imageUrl]
                )
            );
        }

        return $category;
    }

    /**
     * @param string $image
     * @return string
     */
    protected function cropPubFromImageUrl($image)
    {
        if ($this->usePubInMediaUrls === false) {
            $image = str_replace('/pub/', '/', $image);
        }

        return $image;
    }

    /**
     * Add attribute to filter
     *
     * @param int $storeId
     * @param string $attributeCode
     * @param mixed $value
     * @param string $type
     * @param bool $required
     * @param null|string $column
     *
     * @return Select|bool
     * @throws LocalizedException
     */
    protected function _addExtendedFilter(
        $storeId,
        $attributeCode,
        $value,
        $type = '=',
        $required = false,
        $column = null
    ) {
        if (!$this->_select instanceof Select) {
            return false;
        }

        switch ($type) {
            case '=':
                $conditionRule = '=?';
                break;
            case 'in':
                $conditionRule = ' IN(?)';
                break;
            case 'nin':
                $conditionRule = ' NOT IN(?)';
                break;
            default:
                $conditionRule = '';
                break;
        }

        if (!$conditionRule) {
            return $this->_select;
        }

        $attribute = $this->_getAttribute($attributeCode);
        if ($attribute['backend_type'] == 'static') {
            $this->_select->where('e.' . $attributeCode . $conditionRule, $value);
        } else {
            $this->_joinAttribute($storeId, $attributeCode, $column);

            if ($attribute['is_global']) {
                $this->_select->where('t1_' . $attributeCode . '.value' . $conditionRule, $value);
            } else {
                $ifCase = $this->getConnection()->getCheckSql(
                    't2_' . $attributeCode . '.value_id > 0',
                    't2_' . $attributeCode . '.value',
                    't1_' . $attributeCode . '.value'
                );

                if ($required) {
                    $where = '(' . $ifCase . ')' . $conditionRule;
                } else {
                    $where = '(' . $ifCase . ')' . $conditionRule . ' OR ' . '(' . $ifCase . ') IS NULL';
                }

                $this->_select->where($where, $value);
            }
        }

        return $this->_select;
    }

    /**
     * Get attribute data by attribute code
     *
     * @param string $attributeCode
     *
     * @return array
     * @throws LocalizedException
     */
    protected function _getAttribute($attributeCode)
    {
        if (!isset($this->_attributesCache[$attributeCode])) {
            $attribute = $this->_categoryResource->getAttribute($attributeCode);

            $this->_attributesCache[$attributeCode] = [
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal() ==
                    ScopedAttributeInterface::SCOPE_GLOBAL,
                'backend_type'   => $attribute->getBackendType(),
            ];
        }

        return $this->_attributesCache[$attributeCode];
    }

    /**
     * Join attribute by code
     *
     * @param int $storeId
     * @param string $attributeCode
     * @param string $column Add attribute value to given column
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _joinAttribute($storeId, $attributeCode, $column = null)
    {
        $connection     = $this->getConnection();
        $attribute      = $this->_getAttribute($attributeCode);
        $linkField      = $this->_categoryResource->getLinkField();
        $attrTableAlias = 't1_' . $attributeCode;
        $this->_select->joinLeft(
            [$attrTableAlias => $attribute['table']],
            "e.{$linkField} = {$attrTableAlias}.{$linkField}"
            . ' AND ' . $connection->quoteInto($attrTableAlias . '.store_id = ?', Store::DEFAULT_STORE_ID)
            . ' AND ' . $connection->quoteInto($attrTableAlias . '.attribute_id = ?', $attribute['attribute_id']),
            []
        );
        // Global scope attribute value
        $columnValue = 't1_' . $attributeCode . '.value';

        if (!$attribute['is_global']) {
            $attrTableAlias2 = 't2_' . $attributeCode;
            $this->_select->joinLeft(
                ['t2_' . $attributeCode => $attribute['table']],
                "{$attrTableAlias}.{$linkField} = {$attrTableAlias2}.{$linkField}"
                . ' AND ' . $attrTableAlias . '.attribute_id = ' . $attrTableAlias2 . '.attribute_id'
                . ' AND ' . $connection->quoteInto($attrTableAlias2 . '.store_id = ?', $storeId),
                []
            );
            // Store scope attribute value
            $columnValue = $this->getConnection()->getIfNullSql('t2_' . $attributeCode . '.value', $columnValue);
        }

        // Add attribute value to result set if needed
        if (isset($column)) {
            $this->_select->columns(
                [
                    $column => $columnValue
                ]
            );
        }
    }

    /**
     * @return bool
     */
    public function isCollectionReaded()
    {
        return $this->readed;
    }
}
