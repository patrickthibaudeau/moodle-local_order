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

use local_order\vendors;

class vendors_dashboard implements \renderable, \templatable {

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

        $modal = [
            'modal_id' => 'vendorDelete',
            'title' => get_string('delete_vendor', 'local_order'),
            'content' => get_string('delete_vendor_help', 'local_order'),
            'action_button' => 'delete-vendor-confirm',
            'action_button_name' => get_string('delete', 'local_order'),
            'close_button_name' => get_string('cancel', 'local_order'),
        ];

        $alert_modal = [
            'modal_id' => 'vendorAlert',
            'title' => get_string('cannot_delete', 'local_order'),
            'content' => get_string('vendor_used', 'local_order'),
            'close_button_name' => get_string('close', 'local_order'),
        ];

        $data = [
            'vendor_modal' => $modal,
            'alert_modal' => $alert_modal
        ];

        return $data;
    }

}
