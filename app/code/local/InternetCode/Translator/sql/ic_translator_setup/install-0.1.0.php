<?php


/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()->newTable($installer->getTable('ic_translator/missing'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'auto_increment' => true,
    ],
        'ID')
    ->addColumn('module', Varien_Db_Ddl_Table::TYPE_TEXT, 64, [
        'nullable' => false
    ])
    ->addColumn('text', Varien_Db_Ddl_Table::TYPE_VARCHAR, [
        'nullable' => false
    ])
    ->addColumn('fallback', Varien_Db_Ddl_Table::TYPE_VARCHAR)
    ->addColumn('locale', Varien_Db_Ddl_Table::TYPE_TEXT, 12, [
        'nullable' => false
    ])
    ->addIndex(
        $installer->getIdxName('ic_translator/missing', ['locale']),
        ['locale']
    )
    ->addIndex(
        $installer->getIdxName('ic_translator/missing', ['module']),
        ['module']
    );

$installer->getConnection()->createTable($table);


$installer->endSetup();
