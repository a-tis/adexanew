<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Setup\Operation;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddBannerTables
{
    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(BannerInterface::STATIC_TABLE_NAME))
            ->addColumn(
                BannerInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Banner Id'
            )
            ->addColumn(
                BannerInterface::NAME,
                Table::TYPE_TEXT,
                null,
                ['identity' => false, 'nullable' => false],
                'Banner Name'
            )
            ->addColumn(
                BannerInterface::CUSTOMER_GROUP,
                Table::TYPE_TEXT,
                null,
                ['identity' => false, 'nullable' => false],
                'Banner Customer Group'
            )
            ->setComment('Banner Static Table');
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable(BannerInterface::DYNAMIC_TABLE_NAME))
            ->addColumn(
                BannerInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => false, 'unsigned' => true, 'nullable' => false],
                'Banner Id'
            )
            ->addColumn(
                BannerInterface::STATUS,
                Table::TYPE_BOOLEAN,
                null,
                ['identity' => false, 'nullable' => false],
                'Banner Status'
            )
            ->addColumn(
                BannerInterface::IMAGE,
                Table::TYPE_TEXT,
                null,
                ['identity' => false, 'nullable' => false],
                'Banner Image'
            )
            ->addColumn(
                BannerInterface::IMAGE_ALT,
                Table::TYPE_TEXT,
                null,
                ['identity' => false, 'nullable' => true],
                'Banner Image Alt'
            )
            ->addColumn(
                BannerInterface::TARGET_URL,
                Table::TYPE_TEXT,
                null,
                ['identity' => false, 'nullable' => true],
                'Banner Target URL'
            )
            ->addColumn(
                BannerInterface::HOVER_TEXT,
                Table::TYPE_TEXT,
                null,
                ['identity' => false, 'nullable' => true],
                'Banner On Hover Text'
            )
            ->addColumn(
                BannerInterface::STORE_ID,
                Table::TYPE_SMALLINT,
                null,
                ['identity' => false, 'nullable' => false],
                'Banner Store Id'
            )->addIndex(
                $installer->getIdxName(
                    BannerInterface::DYNAMIC_TABLE_NAME,
                    [
                        BannerInterface::ID,
                        BannerInterface::STORE_ID
                    ]
                ),
                [BannerInterface::ID, BannerInterface::STORE_ID],
                ['type' => 'unique']
            )
            ->addIndex(
                $installer->getIdxName(
                    BannerInterface::DYNAMIC_TABLE_NAME,
                    [BannerInterface::ID, BannerInterface::STORE_ID]
                ),
                [BannerInterface::ID, BannerInterface::STORE_ID],
                ['type' => 'unique']
            )
            ->addForeignKey(
                $installer->getFkName(
                    BannerInterface::STATIC_TABLE_NAME,
                    BannerInterface::ID,
                    BannerInterface::DYNAMIC_TABLE_NAME,
                    BannerInterface::ID
                ),
                BannerInterface::ID,
                $installer->getTable(BannerInterface::STATIC_TABLE_NAME),
                BannerInterface::ID,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Banner Dynamic Table');
        $installer->getConnection()->createTable($table);
    }
}
