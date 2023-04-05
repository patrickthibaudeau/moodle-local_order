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

use local_order\vendor;

class vendor_contacts implements \renderable, \templatable {

    public function __construct($id) {
        $this->id = $id;
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

        $VENDOR = new vendor($this->id);
        $data = [
            'vendorid' => $this->id,
            'contacts' => $VENDOR->get_contact_users(),
            'users' => $this->get_users()
        ];
//print_object($data);
        return $data;
    }

    private function get_users() {
        global $DB;

        $sql = "SELECT id, firstname, lastname, email 
                FROM 
                    {user} 
                WHERE 
                    deleted = 0 AND id > 2 
                ORDER BY lastname, firstname";

        $users = $DB->get_records_sql($sql);
        return array_values($users);
    }
}
