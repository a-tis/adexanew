<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Setup\Operation;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Api\Data\SliderInterface;
use Amasty\BannerSlider\Model\OptionSource\Slider\ArrowsStyle;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddSliderTables
{
    const DEFAULT_PAUSE_TIME = 3500;

    public function execute(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(SliderInterface::STATIC_TABLE_NAME))
            ->addColumn(
                SliderInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Slider Id'
            )
            ->addColumn(
                SliderInterface::AUTOPLAY,
                Table::TYPE_BOOLEAN,
                null,
                ['identity' => false, 'nullable' => true],
                'Enable Autoplay'
            )
            ->addColumn(
                SliderInterface::PAUSE_TIME,
                Table::TYPE_SMALLINT,
                null,
                ['identity' => false, 'unsigned' => true, 'nullable' => true, 'default' => self::DEFAULT_PAUSE_TIME],
                'Pause Time Between Transitions, ms'
            )
            ->addColumn(
                SliderInterface::ANIMATION_EFFECT,
                Table::TYPE_SMALLINT,
                null,
                ['identity' => false, 'unsigned' => true, 'nullable' => false],
                'Animation Effect'
            )
            ->addColumn(
                SliderInterface::TRANSITION_SPEED,
                Table::TYPE_SMALLINT,
                null,
                ['identity' => false, 'unsigned' => true, 'nullable' => true],
                'Slide Transition Speed, ms'
            )
            ->addColumn(
                SliderInterface::NAVIGATION_ARROWS,
                Table::TYPE_BOOLEAN,
                null,
                ['identity' => false, 'nullable' => true],
                'Navigation Arrows'
            )
            ->addColumn(
                SliderInterface::ARROWS_STYLE,
                Table::TYPE_SMALLINT,
                null,
                ['identity' => false, 'unsigned' => true, 'nullable' => false, 'default' => ArrowsStyle::FIRST],
                'Arrows Style'
            )
            ->addColumn(
                SliderInterface::NAVIGATION_BULLETS,
                Table::TYPE_BOOLEAN,
                null,
                ['identity' => false, 'nullable' => true],
                'Navigation Bullets'
            )
            ->addColumn(
                SliderInterface::BULLETS_STYLE,
                Table::TYPE_SMALLINT,
                null,
                ['identity' => false, 'unsigned' => true, 'nullable' => false],
                'Bullets Style'
            )
            ->addColumn(
                SliderInterface::BANNER_WIDTH,
                Table::TYPE_SMALLINT,
                null,
                ['identity' => false, 'unsigned' => true, 'nullable' => false],
                'Banner Width, px *'
            )
            ->addColumn(
                SliderInterface::BANNER_HEIGHT,
                Table::TYPE_SMALLINT,
                null,
                ['identity' => false, 'unsigned' => true, 'nullable' => false],
                'Banner Height, px *'
            )
            ->addColumn(
                SliderInterface::RESIZE_IMAGES,
                Table::TYPE_BOOLEAN,
                null,
                ['identity' => false, 'nullable' => true],
                'Resize Images'
            )
            ->setComment('Slider Static Table');
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable(SliderInterface::DYNAMIC_TABLE_NAME))
            ->addColumn(
                SliderInterface::ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => false, 'unsigned' => true, 'nullable' => false],
                'Slider Id'
            )
            ->addColumn(
                SliderInterface::NAME,
                Table::TYPE_TEXT,
                null,
                ['identity' => false, 'nullable' => false],
                'Slider Name'
            )
            ->addColumn(
                SliderInterface::STATUS,
                Table::TYPE_BOOLEAN,
                null,
                ['identity' => false, 'nullable' => true],
                'Slider Status'
            )
            ->addColumn(
                SliderInterface::STORE_ID,
                Table::TYPE_SMALLINT,
                null,
                ['identity' => false, 'nullable' => false],
                'Slider Store Id'
            )
            ->addIndex(
                $installer->getIdxName(
                    SliderInterface::DYNAMIC_TABLE_NAME,
                    [SliderInterface::ID, SliderInterface::STORE_ID]
                ),
                [SliderInterface::ID, SliderInterface::STORE_ID],
                ['type' => 'unique']
            )
            ->addForeignKey(
                $installer->getFkName(
                    SliderInterface::STATIC_TABLE_NAME,
                    SliderInterface::ID,
                    SliderInterface::DYNAMIC_TABLE_NAME,
                    SliderInterface::ID
                ),
                SliderInterface::ID,
                $installer->getTable(SliderInterface::STATIC_TABLE_NAME),
                SliderInterface::ID,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Slider Dynamic Table');
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable(SliderInterface::RELATION_TABLE_NAME))
            ->addColumn(
                SliderInterface::SLIDER_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => false, 'unsigned' => true, 'nullable' => false],
                'Slider Id'
            )
            ->addColumn(
                SliderInterface::BANNER_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => false, 'unsigned' => true, 'nullable' => false],
                'Banner Id'
            )
            ->addColumn(
                SliderInterface::POSITION,
                Table::TYPE_INTEGER,
                null,
                ['identity' => false, 'unsigned' => true, 'nullable' => false],
                'Banner Position'
            )
            ->addIndex(
                $installer->getIdxName(
                    SliderInterface::RELATION_TABLE_NAME,
                    [SliderInterface::SLIDER_ID, SliderInterface::BANNER_ID]
                ),
                [SliderInterface::SLIDER_ID, SliderInterface::BANNER_ID],
                ['type' => 'unique']
            )
            ->addForeignKey(
                $installer->getFkName(
                    SliderInterface::RELATION_TABLE_NAME,
                    SliderInterface::SLIDER_ID,
                    SliderInterface::STATIC_TABLE_NAME,
                    SliderInterface::ID
                ),
                SliderInterface::SLIDER_ID,
                $installer->getTable(SliderInterface::STATIC_TABLE_NAME),
                SliderInterface::ID,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    SliderInterface::RELATION_TABLE_NAME,
                    SliderInterface::BANNER_ID,
                    BannerInterface::STATIC_TABLE_NAME,
                    BannerInterface::ID
                ),
                SliderInterface::BANNER_ID,
                $installer->getTable(BannerInterface::STATIC_TABLE_NAME),
                BannerInterface::ID,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Slider and Banner tables relations');
        $installer->getConnection()->createTable($table);
    }
}
