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


class report_event_cost_by_vendor implements \renderable, \templatable {

    /**
     * @var string
     */
    private $date_range;

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
        $can_edit = false;
        $context = \context_system::instance();

        if (has_capability('local/order:reports_view', $context)) {
           $can_edit = true;
        }

        $VENDORS = new vendors();
        $data = [
            'vendors' => $VENDORS->get_vendors_for_template(),
        ];

        return $data;
    }

}
