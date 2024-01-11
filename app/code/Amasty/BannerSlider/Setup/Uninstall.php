<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


namespace Amasty\BannerSlider\Setup;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Magento\Framework\App\ResourceConnection as AppResource;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @var false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    public function __construct(AppResource $resource)
    {
        $this->connection = $resource->getConnection();
    }

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tablesToDrop = [
            BannerInterface::STATIC_TABLE_NAME,
            BannerInterface::DYNAMIC_TABLE_NAME
        ];

        foreach ($tablesToDrop as $table) {
            $this->connection->dropTable(
                $installer->getTable($table)
            );
        }

        $installer->endSetup();
    }
}
