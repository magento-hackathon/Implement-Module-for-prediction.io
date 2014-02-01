<?php

$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('predictions/queue'))
    ->addColumn('queue_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
    ), 'Queue ID')
    ->addColumn('cookie_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array (
        'nullable'  => false
    ), 'Queue Cookie ID')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array (
        'nullable'  => true,
        'unsigned'  => true
    ), 'Queue Customer ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array (
        'nullable'  => true
    ), 'Queue Product ID')
    ->addColumn('event_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 10, array (
        'nullable'  => false
    ), 'Queue Event Type')
    ->addColumn('cookie_processed', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array (
        'nullable'  => false,
        'default'   => 0
    ), 'Queue - Has the cookie_id data been pushed to the predictions engine?')
    ->addIndex($installer->getIdxName('predictions/queue', array('cookie_id')), array('cookie_id'));

$installer->getConnection()->createTable($table);




$installer->endSetup();
