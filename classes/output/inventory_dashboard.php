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

use local_order\inventory_categories;

class inventory_dashboard implements \renderable, \templatable
{

    /**
     * @var int
     */
    private $category_id;

    public function __construct($category_id)
    {
        $this->category_id = $category_id;
    }

    /**
     *
     * @param \renderer_base $output
     * @return type
     * @global \moodle_database $DB
     * @global type $USER
     * @global type $CFG
     */
    public function export_for_template(\renderer_base $output)
    {
        global $USER, $CFG, $DB;

        $context = \context_system::instance();

        $modal = [
            'modal_id' => 'inventoryDelete',
            'title' => get_string('delete_inventory', 'local_order'),
            'content' => get_string('delete_inventory_help', 'local_order'),
            'action_button' => 'delete-inventory-confirm',
            'action_button_name' => get_string('delete', 'local_order'),
            'close_button_name' => get_string('cancel', 'local_order'),
        ];

        $alert_modal = [
            'modal_id' => 'inventoryAlert',
            'title' => get_string('cannot_delete', 'local_order'),
            'content' => get_string('inventory_item_used', 'local_order'),
            'close_button_name' => get_string('close', 'local_order'),
        ];

        $INVENTORY_CATEGORIES = new inventory_categories();
        $inventory_categories = $INVENTORY_CATEGORIES->get_select_array();
        $categories = [];
        $i = 0;
        foreach ($inventory_categories as $id => $name) {
            $categories[$i]['id'] = $id;
            $categories[$i]['name'] = $name;
            if ($id == $this->category_id) {
                $categories[$i]['selected'] = 'selected';
            } else {
                $categories[$i]['selected'] = '';
            }
            $i++;
        }
        $data = [
            'inventory_modal' => $modal,
            'alert_modal' => $alert_modal,
            'category_id' => $this->category_id,
            'categories' => $categories,
            'can_add' => has_capability('local/order:inventory_add', $context)
        ];

        return $data;
    }

}
