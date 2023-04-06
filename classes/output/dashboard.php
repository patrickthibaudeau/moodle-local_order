<?php
/**
 * *************************************************************************
 * *                           YULearn ELMS                               **
 * *************************************************************************
 * @package     local                                                     **
 * @subpackage  yulearn                                                   **
 * @name        YULearn ELMS                                              **
 * @copyright   UIT - Innovation lab & EAAS                               **
 * @link                                                                  **
 * @author      Patrick Thibaudeau                                        **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************ */

namespace local_order\output;

use local_order\event;
use local_order\events;
use local_order\organization;
use local_order\organizations;
use local_order\vendors;

class dashboard implements \renderable, \templatable {

    public function __construct() {
        
    }

    /**
     * 
     * @global type $USER
     * @global type $CFG
     * @global \moodle_database $DB
     * @param \renderer_base $output
     * @return type
     */
    public function export_for_template(\renderer_base $output) {
        global $USER, $CFG, $DB;
        $context = \context_system::instance();
        // Get number of users
        if ($users = $DB->count_records('user', ['deleted' => 0])) {
            $number_of_users = $users;
        } else {
            $number_of_users = get_string('none', 'local_order');
        }

        $is_vendor = false;
        $vendor_role = $DB->get_record('role', ['shortname' => 'vendor']);
        if (user_has_role_assignment($USER->id, $vendor_role->id, $context->id)) {
            $is_vendor = true;
        }
        $EVENTS = new events();
        $ORGANIZATIONS = new organizations();
        $VENDORS = new vendors();

        $data = [
            'number_of_users' => $number_of_users,
            'number_of_events' => $EVENTS->get_events_count_today(),
            'number_of_organizations' => $ORGANIZATIONS->get_number_of_organizations(),
            'number_of_vendors' => $VENDORS->get_number_of_vendors(),
            'is_vendor' => $is_vendor
        ];

        return $data;
    }

}
