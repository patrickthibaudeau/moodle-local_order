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
use local_order\inventory_categories;

class events_dashboard implements \renderable, \templatable {

    /**
     * @var string
     */
    private $date_range;

    public function __construct($date_range) {
        $this->date_range = $date_range;
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

        $INVENTORY_CATEGORIES = new inventory_categories();
        $inventory_categories_records = $INVENTORY_CATEGORIES->get_records();
        $inventory_categories = [];
        $i = 0;
        foreach ($inventory_categories_records as $icr) {
            $inventory_categories[$i]['id'] = $icr->id;
            $inventory_categories[$i]['name'] = $icr->name;
            $inventory_categories[$i]['code'] = $icr->code;
            $i++;
        }

        $modal = [
            'modal_id' => 'eventDelete',
            'title' => get_string('delete_event', 'local_order'),
            'content' => get_string('delete_event_help', 'local_order'),
            'action_button' => 'delete-event-confirm',
            'action_button_name' => get_string('delete', 'local_order'),
            'close_button_name' => get_string('cancel', 'local_order'),
        ];

        $data = [
            'daterange' => $this->date_range,
            'inventory_categories' => $inventory_categories,
            'event_modal' => $modal
        ];

        return $data;
    }

}
