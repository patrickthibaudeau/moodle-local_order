<?php
defined('MOODLE_INTERNAL') || die;

$systemcontext = context_system::instance();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_order', get_string('pluginname', 'local_order'));
    $ADMIN->add('localplugins',$settings );

    //heading
    $settings->add(new admin_setting_heading(
        'local_order_setting',
        get_string('settings', 'local_order'),
        ''
    ));
    // Sample text
    $settings->add(new admin_setting_configtext(
        'local_order_organizer_account',
        get_string('organizer_account', 'local_order'),
        get_string('organizer_account_help', 'local_order'),
        '',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        'local_order_pst',
        get_string('pst', 'local_order'),
        get_string('pst_help', 'local_order'),
        '8',
        PARAM_INT
    ));
    $settings->add(new admin_setting_configtext(
        'local_order_gst',
        get_string('gst', 'local_order'),
        get_string('gst_help', 'local_order'),
        '5',
        PARAM_INT
    ));
    $settings->add(new admin_setting_configtext(
        'local_order_hst_number',
        get_string('hst_number', 'local_order'),
        get_string('hst_number_help', 'local_order'),
        '',
        PARAM_TEXT
    ));
}





