<?php

namespace local_order;

//TO DO: Change this into a Singleton and get rid of static functions
class base
{

    /**
     * Creates the Moodle page header
     * @param string $url Current page url
     * @param string $pagetitle Page title
     * @param string $pageheading Page heading (Note hard coded to site fullname)
     * @param array $context The page context (SYSTEM, COURSE, MODULE etc)
     * @param string $pagelayout The page context (SYSTEM, COURSE, MODULE etc)
     * @return HTML Contains page information and loads all Javascript and CSS
     * @global \stdClass $CFG
     * @global \moodle_database $DB
     * @global \moodle_page $PAGE
     * @global \stdClass $SITE
     */
    public static function page($url, $pagetitle, $pageheading, $context = null, $pagelayout = 'base')
    {
        global $CFG, $PAGE, $SITE;


        $context = \context_system::instance();


        $PAGE->set_url($url);
        $PAGE->set_title($pagetitle);
        $PAGE->set_heading($pageheading);
        $PAGE->set_pagelayout($pagelayout);
        $PAGE->set_context($context);
        // We need datatables to work. So we load it from cdn
        // We also load one JS file that initialises all datatables.
        // This same file is used throughout, including in the blocks
        self::loadJQueryJS();
    }

    public static function loadJQueryJS()
    {
        global $CFG, $PAGE;
        $stringman = get_string_manager();
        $strings = $stringman->load_component_strings('local_order', current_language());

        $PAGE->requires->jquery();
        $PAGE->requires->jquery_plugin('ui');
        $PAGE->requires->jquery_plugin('ui-css');

        $PAGE->requires->strings_for_js(array_keys($strings), 'local_order');
    }

    /**
     * Sets filemanager options
     * @param \stdClass $context
     * @param int $maxfiles
     * @return array
     * @global \stdClass $CFG
     */
    public static function get_file_manager_options($context, $maxfiles = 1)
    {
        global $CFG;
        return array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => $maxfiles);
    }

    public static function get_file_picker_import_ptions($context, $maxfiles = 1)
    {
        global $CFG;
        return array('maxbytes' => $CFG->maxbytes, 'accepted_types' => ".xlsx");
    }

    public static function get_editor_options($context)
    {
        global $CFG;
        return array('subdirs' => 1, 'maxbytes' => $CFG->maxbytes, 'maxfiles' => -1,
            'changeformat' => 1, 'context' => $context, 'noclean' => 1, 'trusttext' => 0);
    }
}
