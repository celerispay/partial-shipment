<?php

namespace Boostsales\PartialShipment\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'is_partial_delivery',
            [
                'type' => 'boolean',
                'nullable' => true,
                'comment' => 'Partial delivery',
                'default' => 0
            ]
        );

        $setup->endSetup();
    }
}