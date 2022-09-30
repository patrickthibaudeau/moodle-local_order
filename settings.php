<?php
defined('MOODLE_INTERNAL') || die;

$systemcontext = context_system::instance();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_order', get_string('pluginname', 'local_order'));
    $ADMIN->add('localplugins',$settings );

    // SAMPLE heading
//    $settings->add(new admin_setting_heading(
//        'halo_setting',
//        get_string('halo_settings', 'local_order'),
//        ''
//    ));
    // Sample text
//    $settings->add(new admin_setting_configtext(
//        'halo_tenant',
//        get_string('tenant', 'local_order'),
//        get_string('tenant_help', 'local_order'),
//        '',
//        PARAM_TEXT
//    ));



}





