<?php

$functions = array(

    'local_order_get_users' => array(
        'classname' => 'local_order_external_user',
        'methodname' => 'get_users',
        'classpath' => 'local/order/classes/external/user.php',
        'description' => 'Get users',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true
    ),
    'local_order_get_organizations' => array(
        'classname' => 'local_order_external_organization',
        'methodname' => 'get_organizations',
        'classpath' => 'local/order/classes/external/organization.php',
        'description' => 'Get organizations',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true
    ),
    'local_order_get_event_types' => array(
        'classname' => 'local_order_external_event_type',
        'methodname' => 'get_event_types',
        'classpath' => 'local/order/classes/external/event_type.php',
        'description' => 'Get event types',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true
    ),
);

