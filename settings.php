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
        'local_order_org_email',
        get_string('organizer_account', 'local_order'),
        get_string('organizer_account_help', 'local_order'),
        '',
        PARAM_TEXT
    ));
}





