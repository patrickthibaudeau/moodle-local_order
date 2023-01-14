<?php
/*
 * Author: Admin User
 * Create Date: 13-12-2022
 * License: LGPL 
 * 
 */

namespace local_order;

class inventories
{

    /**
     *
     * @var string
     */
    private $results;

    /**
     *
     * @global \moodle_database $DB
     */
    public function __construct()
    {
        global $DB;
        $this->results = $DB->get_records('order_inventory', [], "name");
    }

    /**
     * Get records
     */
    public function get_records()
    {
        return $this->results;
    }

    /**
     * Get records based on inventory category
     * @param $inventory_category int default 1 Audovisual
     * @return array
     * @throws \dml_exception
     */
    public function get_records_by_category($inventory_category = 1)
    {
        global $DB;

        return $DB->get_records(TABLE_INVENTORY, ['inventorycategoryid' => $inventory_category], "name");
    }

    /**
     * Array to be used for selects
     * Defaults used key = record id, value = name
     * Modify as required.
     */
    public function get_select_array()
    {
        $array = [
            '' => get_string('select', 'local_order')
        ];
        foreach ($this->results as $r) {
            $array[$r->id] = $r->name;
        }
        return $array;
    }

    /**
     * @param $category_id
     * @param $start
     * @param $end
     * @param $term
     * @param $order_column
     * @param $order_direction
     * @return \stdClass
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     * @throws \require_login_exception
     */
    public function get_datatable($category_id, $start, $end, $term, $order_column = 'name', $order_direction = 'ASC')
    {
        global $CFG, $DB, $OUTPUT, $PAGE;

        $context = \context_system::instance();
        $PAGE->set_context($context);
        require_login(1, false);


        $sql = "Select
                    i.id,
                    i.name,
                    i.code,
                    ic.name As category,
                    i.cost
                From
                    {order_inventory_category} ic Inner Join
                    {order_inventory} i On i.inventorycategoryid = ic.id";

        if ($category_id) {
            $sql .= " Where i.inventorycategoryid = $category_id";
        }

        if ($term) {
            if ($category_id) {
                $sql .= " AND (i.name LIKE '%$term%' ";
            } else {
                $sql .= " WHERE (i.name LIKE '%$term%' ";
            }
            $sql .= " OR i.code LIKE '%$term%' ";
            $sql .= " OR i.cost LIKE '%$term%' ";
            $sql .= " OR ic.name LIKE '%$term%') ";
        }

        $total_found = count($DB->get_records_sql($sql));

        switch ($order_column) {
            case 'shortname':
                $order_column = 'i.code';
                break;
            case 'category':
                $order_column = 'ic.name';
                break;
            case 'cost':
                $order_column = 'i.cost';
                break;
            case 'name':
                $order_column = 'i.name';
                break;
            default:
                $order_column = 'i.name';
                break;

        };
        $sql .= " Order by $order_column $order_direction";
        $sql .= " LIMIT $start, $end";

        $results = $DB->get_recordset_sql($sql);

        $inventory = [];
        $i = 0;
        // To use with cost to format with current currency
        $amount = new \NumberFormatter(get_string('currency_locale', 'local_order'),
            \NumberFormatter::CURRENCY);

        foreach ($results as $r) {
            $actions = [
                'id' => $r->id,
                'type' => 'inventory'
            ];
            $inventory[$i]['id'] = $r->id;
            $inventory[$i]['name'] = $r->name;
            $inventory[$i]['shortname'] = $r->code;
            $inventory[$i]['cost'] = $amount->format($r->cost);
            $inventory[$i]['category'] = $r->category;
            $inventory[$i]['actions'] = $OUTPUT->render_from_template('local_order/action_buttons', $actions);;
            $i++;
        }

        $data = new \stdClass();
        $data->total_found = $total_found;
        $data->total_displayed = count($inventory);
        $data->results = $inventory;

        return $data;

    }

}