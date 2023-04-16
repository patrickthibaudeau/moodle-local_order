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
use local_order\room_basics;

class events_dashboard implements \renderable, \templatable {

    /**
     * @var string
     */
    private $date_range;

    public function __construct(
        $date_range,
        $building_shortname = null,
        $room_name = null,
        $status = -1,
        $organization = -1
    ) {
        $this->date_range = $date_range;
        $this->building_shortname = $building_shortname;
        $this->room_name = $room_name;
        $this->status = $status;
        $this->organization = $organization;
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

        $can_approve = has_capability('local/order:event_approve', $context);

        $BUILDINGS = new room_basics();
        $template_rooms = [];
        if ($this->room_name) {
            $ROOMS = new room_basics();
            $template_rooms = $ROOMS->get_rooms_based_on_building_for_template($this->building_shortname, $this->room_name);
        }

        $ORGANIZATIONS = new organizations();

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

        $statuses = [
            0 => get_string('all', 'local_order'),
            1 => get_string('new', 'local_order'),
            2 => get_string('approved', 'local_order'),
            3 => get_string('pending', 'local_order'),
            4 => get_string('cancelled', 'local_order'),
        ];

        $status_array = [];
        foreach($statuses as $key => $text) {

                $status_array[$key]['text'] = $text;
                if ($key == 0) {
                    $status_array[$key]['value'] = -1;
                } else {
                    $status_array[$key]['value'] = $key - 1;
                }
                if ($key - 1 == $this->status) {
                    $status_array[$key]['selected'] = 'selected';
                }

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
            'event_modal' => $modal,
            'rooms' => $template_rooms,
            'statuses' => $status_array,
            'can_approve' => $can_approve,
            'buildings' => $BUILDINGS->get_buildings_for_template($this->building_shortname),
            'organizations' => $ORGANIZATIONS->get_organizations_for_template($this->organization),
        ];

        return $data;
    }

}
