<?php

$installer = $this;

$installer->startSetup();

$queue_table = $installer->getConnection()
    ->newTable($installer->getTable('predictions/queue'))
    ->addColumn('queue_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
    ), 'Queue ID')
    ->addColumn('cookie_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array (
        'nullable'  => false,
        'unsigned'  => true
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

$installer->getConnection()->createTable($queue_table);

$recommendation_table = $installer->getConnection()
    ->newTable($installer->getTable('predictions/recommendation'))
    ->addColumn('recommendation_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
    ), 'Recommendation ID')
    ->addColumn('cookie_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array (
        'nullable'  => false,
        'unsigned'  => true
    ), 'Recommendation Cookie ID')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array (
        'nullable'  => true,
        'unsigned'  => true
    ), 'Recommendation Customer ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array (
        'nullable'  => false,
        'unsigned'  => true,
    ), 'Recommendation Product ID')
    ->addColumn('recommended_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array (
        'nullable'  => false
    ), 'Recommended At Timestamp');
$installer->getConnection()->createTable($recommendation_table);


$installer->endSetup();
