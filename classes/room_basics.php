<?php
/*
 * Author: Admin User
 * Create Date: 9-04-2023
 * License: LGPL 
 * 
 */
namespace local_order;

class room_basics {

	/**
	 *
	 *@var string
	 */
	private $results;

	/**
	 *
	 *@global \moodle_database $DB
	 */
	public function __construct() {
	    global $DB;
	    $this->results = $DB->get_records('order_room_basic');
	}

	/**
	  * Get records
	 */
	public function get_records() {
	    return $this->results;
	}

	/**
	  * Array to be used for selects
	  * Defaults used key = record id, value = name 
	  * Modify as required. 
	 */
	public function get_select_array() {
	    $array = [
	        '' => get_string('select', 'local_order')
	      ];
	      foreach($this->results as $r) {
	            $array[$r->id] = $r->name;
	      }
	    return $array;
	}

    /**
     * Returns an multidimensional array with campuses and their buildings
     * @return array
     * @throws \dml_exception
     */
    public function get_buildings_for_template($selected = null) {
        global $DB;

        $sql = "SELECT DISTINCT 
                    building_name, 
                    building_shortname
                FROM
                    {order_room_basic}
                ORDER BY 
                    building_shortname";

        $buildings = $DB->get_recordset_sql($sql);
        $buildings_list = [];
        $i = 0;
        foreach($buildings as $building) {
            if ($selected == $building->building_shortname) {
                $buildings_list[$i]['selected'] = true;
            } else {
                $buildings_list[$i]['selected'] = false;
            }
            $buildings_list[$i]['building_name'] = $building->building_name;
            $buildings_list[$i]['building_shortname'] = $building->building_shortname;
            $i++;
        }

        return $buildings_list;
    }

    /**
     * Returns an multidimensional array with campuses and their buildings
     * @return array
     * @throws \dml_exception
     */
    public function get_rooms_based_on_building_for_template($building_shortname, $selected = null) {
        global $DB;

        $sql = "SELECT DISTINCT 
                    name, 
                    building_shortname
                FROM
                    {order_room_basic}
                WHERE 
                    building_shortname = ?
                ORDER BY 
                    name";

        $rooms = $DB->get_recordset_sql($sql, [$building_shortname]);
        $room_list = [];
        $i = 0;
        foreach($rooms as $room) {
            if ($selected == $room->name) {
                $room_list[$i]['selected'] = true;
            } else {
                $room_list[$i]['selected'] = false;
            }
            $room_list[$i]['name'] = $room->name;
            $room_list[$i]['building_shortname'] = $room->building_shortname;
            $i++;
        }

        return $room_list;
    }


    /**
     * Returns an multidimensional array with campuses and their buildings
     * @return array
     * @throws \dml_exception
     */
    public function get_buildings_array() {
        global $DB;

        $sql = "SELECT DISTINCT 
                    building_name, 
                    building_shortname
                FROM
                    {order_room_basic}
                ORDER BY 
                    building_name";

        $buildings = $DB->get_recordset_sql($sql);
        $buildings_list = [];
        foreach($buildings as $building) {
            $buildings_list[$building->building_shortname] = $building->building_name . ' (' . $building->building_shortname . ')';
        }

        return $buildings_list;
    }

    /**
     * Returns an multidimensional array with campuses and their buildings
     * @return array
     * @throws \dml_exception
     */
    public function get_rooms_based_on_building_for_js($building_shortname) {
        global $DB;

        $sql = "SELECT DISTINCT 
                    name, 
                    building_shortname
                FROM
                    {order_room_basic}
                WHERE 
                    building_shortname = ?
                ORDER BY 
                    name";

        $rooms = $DB->get_recordset_sql($sql, [$building_shortname]);
        $room_list = [];
        foreach($rooms as $room) {
            $room_list[$room->name] =  $room->name;
        }

        return $room_list;
    }

    public function get_rooms_for_form() {
        global $DB;

        $data = [];
        $buildings_sql = "SELECT DISTINCT
                    id,  
                    building_name, 
                    building_shortname
                FROM
                    {order_room_basic}
                ORDER BY 
                    building_name";
        $builldings = $DB->get_records_sql($buildings_sql);


        foreach($builldings as $building) {
            $rooms_sql = "SELECT 
                            id,
                            name,
                            building_shortname 
                        FROM 
                            {order_room_basic} 
                        WHERE 
                            building_shortname = ? 
                        ORDER BY name";
            $rooms = $DB->get_records_sql($rooms_sql, [$building->building_shortname]);
            $rooms_array = [];
            foreach($rooms as $r) {
                $rooms_array[$r->id] = $r->building_shortname . ' ' . $r->name;
            }
            $data[$building->building_name] = $rooms_array;

        }

        return $data;
    }

    /**
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
    public function get_datatable( $start, $end, $term, $order_column = 'name', $order_direction = 'ASC')
    {
        global $CFG, $DB, $OUTPUT, $PAGE;

        $context = \context_system::instance();
        $PAGE->set_context($context);
        require_login(1, false);

        $can_edit = has_capability('local/order:room_edit', $context);
        $can_delete = has_capability('local/order:room_delete', $context);

        $sql = "Select * From {order_room_basic} ";


        if ($term) {
            $sql .= " WHERE (building_name LIKE '%$term%' ";
            $sql .= " OR building_shortname LIKE '%$term%' ";
            $sql .= " OR name LIKE '%$term%') ";
        }

        $total_found = count($DB->get_records_sql($sql));


        $sql .= " Order by $order_column $order_direction";
        $sql .= " LIMIT $start, $end";

        $results = $DB->get_recordset_sql($sql);

        $rooms = [];
        $i = 0;


        foreach ($results as $r) {
            $actions = [
                'id' => $r->id,
                'type' => 'room',
                'can_edit' => $can_edit,
                'can_delete' => $can_delete,
                'can_add' => true,
            ];
            $rooms[$i]['id'] = $r->id;
            $rooms[$i]['building_name'] = $r->building_name;
            $rooms[$i]['building_shortname'] = $r->building_shortname;
            $rooms[$i]['name'] = $r->name;
            $rooms[$i]['actions'] = $OUTPUT->render_from_template('local_order/action_buttons', $actions);;
            $i++;
        }

        $data = new \stdClass();
        $data->total_found = $total_found;
        $data->total_displayed = count($rooms);
        $data->results = $rooms;

        return $data;

    }

}