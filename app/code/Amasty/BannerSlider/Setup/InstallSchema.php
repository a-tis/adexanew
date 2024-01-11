<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


namespace Amasty\BannerSlider\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Operation\AddBannerTables
     */
    private $addBannerTables;

    /**
     * @var Operation\AddSliderTables
     */
    private $addSliderTables;

    public function __construct(
        Operation\AddBannerTables $addBannerTables,
        Operation\AddSliderTables $addSliderTables
    ) {
        $this->addBannerTables = $addBannerTables;
        $this->addSliderTables = $addSliderTables;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->addBannerTables->execute($setup);
        $this->addSliderTables->execute($setup);
        $setup->endSetup();
    }
}
