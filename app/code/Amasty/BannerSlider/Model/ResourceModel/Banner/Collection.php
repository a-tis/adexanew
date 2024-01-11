<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Model\ResourceModel\Banner;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Model\Banner;
use Amasty\BannerSlider\Model\ResourceModel\Banner as ResourceBanner;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_setIdFieldName(BannerInterface::ID);
        $this->_init(Banner::class, ResourceBanner::class);
        $this->addFilterToMap(BannerInterface::ID, 'main_table.' . BannerInterface::ID);
    }

    public function addDynamicData(): Collection
    {
        $dynamicContentTable = $this->getResource()->getTable(BannerInterface::DYNAMIC_TABLE_NAME);
        //TODO fix store
        $this->getSelect()->joinInner(
            $dynamicContentTable,
            sprintf(
                '%s.id = main_table.id AND %s.store_id = %s',
                $dynamicContentTable,
                $dynamicContentTable,
                Store::DEFAULT_STORE_ID
            ),
            BannerInterface::DYNAMIC_FIELDS
        );

        return $this;
    }

    public function addDynamicTable(int $storeId = 0, array $columns = BannerInterface::DYNAMIC_FIELDS)
    {
        $dynamicContentTable = $this->getTable(BannerInterface::DYNAMIC_TABLE_NAME);

        $this->joinDynamicTable($dynamicContentTable, Store::DEFAULT_STORE_ID);
        if ($storeId !== Store::DEFAULT_STORE_ID) {
            $this->joinDynamicTable($dynamicContentTable, $storeId);
            $this->selectColumns($dynamicContentTable, $columns, $storeId);
        }
    }

    private function joinDynamicTable(string $dynamicContentTable, int $storeId = 0)
    {
        $alias = sprintf('%s_%s', BannerInterface::DYNAMIC_TABLE_NAME, $storeId);
        $this->getSelect()->joinLeft(
            [$alias => $dynamicContentTable],
            sprintf('%s.id = main_table.id AND %s.store_id = %s', $alias, $alias, $storeId)
        );
    }

    private function selectColumns(string $dynamicContentTable, array $columns, int $storeId = 0)
    {
        $currentColumns = [];
        foreach ($columns as $column) {
            $currentColumns[$column] = $this->getConnection()->getIfNullSql(
                sprintf('%s_%s.%s', BannerInterface::DYNAMIC_TABLE_NAME, $storeId, $column),
                sprintf('%s_%s.%s', BannerInterface::DYNAMIC_TABLE_NAME, Store::DEFAULT_STORE_ID, $column)
            );
        }
        $this->getSelect()->columns($currentColumns);
    }
}
