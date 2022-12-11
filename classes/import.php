<?php

namespace local_order;

require_once($CFG->libdir . '/phpspreadsheet/vendor/autoload.php');

use core\notification;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class import
{
    private $worksheet;

    /**
     * @param $file string  Path to file
     */
    public function __construct($file = '')
    {
        if ($file) {
            // Make sure we have an .xlsx file
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_info = finfo_file($finfo, $file);

            if ($file_info == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $spread_sheet = $reader->load($file);
                $this->worksheet = $spread_sheet->getActiveSheet();;
            } else {
                notification::error('You must upload an xlsx file');
                $this->worksheet = false;
            }
        }
    }

    /**
     * Returns an array of all columns in the first row of the work sheet
     * @return array
     */
    public function get_first_row()
    {
        $worksheet = $this->worksheet;
        $worksheet_array = $worksheet->toArray();
//        print_object($worksheet_array);
        $columns = [];
        for ($i = 0; $i < count($worksheet_array[0]); $i++) {
            if ($worksheet_array[0][$i]) {
                $columns[$i] = $worksheet_array[0][$i];
            }
        }
        return $columns;
    }

    /**
     * Returns all rows as an array
     * @return array
     */
    public function get_rows()
    {
        raise_memory_limit(MEMORY_UNLIMITED);
        $worksheet = $this->worksheet;
        $worksheet_array = $worksheet->toArray();
        $number_of_rows = count($worksheet_array);
        $columns = $this->get_first_row();
        $data = [];
        $rows = [];
        // Start at 1 because 0 is the first row
        for ($i = 1; $i <= $number_of_rows; $i++) {

            foreach ($columns as $key => $column) {
                if (isset($worksheet_array[$i][$key])) {
                    $rows[$i][$key] = $worksheet_array[$i][$key];
                } else {
                    $rows[$i][$key] = '';
                }
            }

        }
        raise_memory_limit(MEMORY_STANDARD);
        return $rows;
    }

    /**
     * Returns array for colum names
     * @return array
     */
    public function clean_column_names()
    {
        $columns = $this->get_first_row();
        $column_names = [];
        foreach ($columns as $key => $column) {
            $column_names[$key] = new \stdClass();
            $column_names[$key]->fullname = $column;
            // Clean the column name
            $clean_column = preg_replace('/[^\w\s]+/', '', $column);;
            $clean_column = str_replace(" ", '_', $clean_column);
            $clean_column = strtolower($clean_column);
            $column_names[$key]->shortname = $clean_column;

        }

        return $column_names;
    }

    /**
     * Verifiy that certain tables have data before import can be performed.
     * @param $import_type
     * @return void
     */
    public function can_import($import_type)
    {
        global $DB;

        switch ($import_type) {
            case 'floor':
                if ($buildings = $DB->count_records(TABLE_BUILDING, [])) {
                    return true;
                } else {
                    $data = [TABLE_BUILDING];
                    return $data;
                }
                break;
            case 'room':
                $buildings = $DB->count_records(TABLE_BUILDING, []);
                $floors = $DB->count_records(TABLE_FLOOR, []);
                $room_types = $DB->count_records(TABLE_ROOM_TYPE, []);
                if ($buildings && $floors && $room_types) {
                    return true;
                } else {
                    $data = [TABLE_BUILDING, TABLE_FLOOR, TABLE_ROOM_TYPE];
                    return $data;
                }
                break;
            default :
                return true;
                break;
        }
    }

    /**
     * @param $columns array
     * @param $rows array
     * @return void
     */
    public function campus($columns, $rows)
    {
        global $CFG, $DB, $USER;

        // Make sure the columns exist
        if (!in_array('code', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=code');
        }
        if (!in_array('name', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=name');
        }
        // Set the proper column key
        $code = 0;
        $name = 1;
        // Set the proper key value for the columns
        foreach ($columns as $key => $name) {
            switch ($name) {
                case 'code':
                    $code = $key;
                    break;
                case 'name':
                    $name = $key;
                    break;
            }
        }

        // Import campus data if it doesn;t already exists.
        for ($i = 1; $i < count($rows) - 1; $i++) {
            if (!$found = $DB->get_record(TABLE_CAMPUS, ['code' => trim($rows[$i][$code])])) {
                // Insert into table
                $params = new \stdClass();
                $params->code = trim($rows[$i][$code]);
                $params->name = trim($rows[$i][$name]);
                $params->timecreated = time();
                $params->timemodified = time();
                $params->usermodified = $USER->id;

                $DB->insert_record(TABLE_CAMPUS, $params);
                notification::success('Campus ' . $params->name . ' has been added.');
            } else {
                notification::WARNING('Campus ' . $rows[$i][$name] . ' already exists.');
            }
        }

        return true;
    }

    /**
     * @param $columns array
     * @param $rows array
     * @return void
     */
    public function building($columns, $rows)
    {
        global $CFG, $DB, $USER;

        // Make sure the columns exist
        if (!in_array('campuscode', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=campuscode');
        }
        if (!in_array('code', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=code');
        }
        if (!in_array('name', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=name');
        }
        if (!in_array('shortname', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=shortname');
        }
        // Set the proper column key
        $campus_code = 0;
        $code = 0;
        $name = 0;
        $short_name = 0;
        // Set the proper key value for the columns
        foreach ($columns as $key => $name) {
            switch ($name) {
                case 'campuscode':
                    $campus_code = $key;
                    break;
                case 'code':
                    $code = $key;
                    break;
                case 'name':
                    $name = $key;
                    break;
                case 'shortname':
                    $short_name = $key;
                    break;
            }
        }

        // Import campus data if it doesn;t already exists.
        for ($i = 1; $i < count($rows) - 1; $i++) {
            if (!$found = $DB->get_record(TABLE_BUILDING, ['code' => trim($rows[$i][$code])])) {
                // Insert into table
                $params = new \stdClass();
                $params->campus_code = trim($rows[$i][$campus_code]);
                $params->code = trim($rows[$i][$code]);
                $params->name = trim($rows[$i][$name]);
                $params->shortname = trim($rows[$i][$short_name]);
                $params->timecreated = time();
                $params->timemodified = time();
                $params->usermodified = $USER->id;

                $DB->insert_record(TABLE_BUILDING, $params);
                notification::success('Building ' . $params->name . ' has been added.');
            } else {
                notification::WARNING('Building ' . $rows[$i][$name] . ' already exists.');
            }
        }

        return true;
    }

    /**
     * @param $columns array
     * @param $rows array
     * @return void
     */
    public function floor($columns, $rows)
    {
        global $CFG, $DB, $USER;

        // Make sure the columns exist
        if (!in_array('code', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=code');
        }
        if (!in_array('building_code', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=building_code');
        }
        // Set the proper column key
        $code = 0;
        $building_code = 1;
        // Set the proper key value for the columns
        foreach ($columns as $key => $name) {
            switch ($name) {
                case 'code':
                    $code = $key;
                    break;
                case 'building_code':
                    $building_code = $key;
                    break;
            }
        }

        // Import campus data if it doesn;t already exists.
        for ($i = 1; $i < count($rows) - 1; $i++) {
            if (!$found = $DB->get_record(TABLE_FLOOR, ['building_code' => trim($rows[$i][$building_code]), 'code' => trim($rows[$i][$code])])) {
                // Insert into table
                $params = new \stdClass();
                $params->code = trim($rows[$i][$code]);
                $params->building_code = trim($rows[$i][$building_code]);
                $params->timecreated = time();
                $params->timemodified = time();
                $params->usermodified = $USER->id;

                $DB->insert_record(TABLE_FLOOR, $params);
                notification::success('Floor ' . $params->code . ' for building code ' . $params->building_code . ' has been added.');
            } else {
                notification::WARNING('Floor ' . $rows[$i][$code] . ' for building code ' . $rows[$i][$building_code] . ' already exists.');
            }
        }

        return true;
    }

    /**
     * @param $columns array
     * @param $rows array
     * @return void
     */
    public function room_type($columns, $rows)
    {
        global $CFG, $DB, $USER;
        raise_memory_limit(MEMORY_UNLIMITED);
        // Make sure the columns exist
        if (!in_array('name', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=name');
        }

        // Set the proper column key
        $name1 = 0;
        // Set the proper key value for the columns
        foreach ($columns as $key => $name) {
            switch ($name) {
                case 'name':
                    $name1 = $key;
                    break;
            }
        }

        // Import campus data if it doesn;t already exists.
        for ($i = 1; $i < count($rows) - 1; $i++) {
            if (!$found = $DB->get_record(TABLE_ROOM_TYPE, ['name' => trim($rows[$i][$name1])])) {
                // Insert into table
                $params = new \stdClass();
                $params->name = trim($rows[$i][$name1]);
                $params->timecreated = time();
                $params->timemodified = time();
                $params->usermodified = $USER->id;

                $DB->insert_record(TABLE_ROOM_TYPE, $params);
                notification::success('Room type ' . $params->name . ' has been added.');
            } else {
                notification::WARNING('Room type ' . $rows[$i][$name1] . ' already exists.');
            }
        }
        raise_memory_limit(MEMORY_STANDARD);
        return true;
    }

    /**
     * @param $columns array
     * @param $rows array
     * @return void
     */
    public function room($columns, $rows)
    {
        global $CFG, $DB, $USER;
        raise_memory_limit(MEMORY_UNLIMITED);
        // Make sure the columns exist
        if (!in_array('name', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=name');
        }
        if (!in_array('building_code', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=building_code');
        }
        if (!in_array('floor_code', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=floor_code');
        }
        if (!in_array('code', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=code');
        }
        if (!in_array('room_type', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=room_type');
        }

        // Set the proper column key
        $name1 = 0;
        $building_code = 0;
        $floor_code = 0;
        $code = 0;
        $room_type = 0;
        $capacity = 0;
        // Set the proper key value for the columns
        foreach ($columns as $key => $name) {
            switch ($name) {
                case 'name':
                    $name1 = $key;
                    break;
                case 'building_code':
                    $building_code = $key;
                    break;
                case 'floor_code':
                    $floor_code = $key;
                    break;
                case 'code':
                    $code = $key;
                    break;
                case 'room_type':
                    $room_type = $key;
                    break;
                case 'capacity':
                    $capacity = $key;
                    break;
            }
        }

        ob_start();
        // Import data if it doesn't already exists.
        for ($i = 1; $i < count($rows) - 1; $i++) {
            // Get room floor
            $floor = $DB->get_record(TABLE_FLOOR,
                [
                    'building_code' => trim($rows[$i][$building_code]),
                    'code' => trim($rows[$i][$floor_code])
                ]);
            $room_type_name = trim($rows[$i][$room_type]);
            // Get room type
            $room_type_data = $DB->get_record(TABLE_ROOM_TYPE, ['name' => $room_type_name]);

            if (!$found = $DB->get_record(TABLE_ROOM,
                [
                    'floor_id' => $floor->id,
                    'room_type_id' => $room_type_data->id,
                    'code' => trim($rows[$i][$code])
                ])) {
                // Insert into table
                $params = new \stdClass();
                $params->floor_id = $floor->id;
                $params->room_type_id = $room_type_data->id;
                $params->code = trim($rows[$i][$code]);
                $params->name = trim($rows[$i][$name1]);
                $params->capacity = trim($rows[$i][$capacity]);
                $params->timecreated = time();
                $params->timemodified = time();
                $params->usermodified = $USER->id;

                $DB->insert_record(TABLE_ROOM, $params);
                notification::success('Room ' . $params->code . ' has been added.');
            } else {
                notification::WARNING('Room ' . $rows[$i][$code] . ' already exists.');
            }
            ob_flush();
            flush();
        }
        ob_clean();
        raise_memory_limit(MEMORY_STANDARD);
        return true;
    }

    /**
     * @param $columns array
     * @param $rows array
     * @return void
     */
    public function inventory($columns, $inventory_category)
    {
        global $CFG, $DB, $USER;
        raise_memory_limit(MEMORY_UNLIMITED);

        ob_start();
        // Import data if it doesn't already exists.
        foreach ($columns as $inventory) {

            if (!$found = $DB->get_record(TABLE_INVENTORY, [
                'inventorycategoryid' => $inventory_category,
                'code' => $inventory->shortname
            ])) {
                // Insert into table
                $params = new \stdClass();
                $params->inventorycategoryid = $inventory_category;
                $params->name = $inventory->fullname;
                $params->code = $inventory->shortname;
                $params->timecreated = time();
                $params->timemodified = time();
                $params->usermodified = $USER->id;

                $DB->insert_record(TABLE_INVENTORY, $params);
                notification::success('Inventory item ' . $params->code . ' has been added.');
            } else {
                notification::WARNING('Inventory item ' . $inventory->code . ' already exists.');
            }
            ob_flush();
            flush();
        }
        ob_clean();
        raise_memory_limit(MEMORY_STANDARD);
        return true;
    }

    /**
     * @param $columns array
     * @param $rows array
     * @return void
     */
    public function organization($columns, $rows)
    {
        global $CFG, $DB, $USER;

        // Make sure the columns exist
        if (!in_array('name', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=name');
        }
        if (!in_array('firstname', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=firstname');
        }
        if (!in_array('lastname', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=lastname');
        }
        if (!in_array('email', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=email');
        }
        if (!in_array('phone1', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=phone1');
        }
        if (!in_array('phone2', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=phone2');
        }
        // Set the proper column key
        $organization = 0;
        $first_name = 0;
        $last_name = 0;
        $email = 0;
        $phone1 = 0;
        $phone2 = 0;
        // Set the proper key value for the columns
        foreach ($columns as $key => $name) {
            switch ($name) {
                case 'name':
                    $organization = $key;
                    break;
                case 'firstname':
                    $first_name = $key;
                    break;
                case 'lastname':
                    $last_name = $key;
                    break;
                case 'email':
                    $email = $key;
                    break;
                case 'phone1':
                    $phone1 = $key;
                    break;
                case 'phone2':
                    $phone2 = $key;
                    break;
            }
        }

        // Import campus data if it doesn;t already exists.
        for ($i = 1; $i < count($rows) - 1; $i++) {
            $fields = explode(' - ', $rows[$i][$organization]);
            if (count($fields) == 2) {
                $code = trim($fields[0]);
                $name = trim($fields[1]);
            } else {
                $code = '';
                $name = trim($fields[0]);
            }


            if (!$found = $DB->get_record(TABLE_ORGANIZATION, ['name' => $name])) {
                // Insert into table
                $params = new \stdClass();
                $params->name = $name;
                $params->code = $code;
                $params->phone = trim($rows[$i][$phone1]);
                $params->email = trim($rows[$i][$email]);
                $params->timecreated = time();
                $params->timemodified = time();
                $params->usermodified = $USER->id;

                $organizationid = $DB->insert_record(TABLE_ORGANIZATION, $params);

                if (trim($rows[$i][$first_name]) && trim($rows[$i][$last_name]) && trim($rows[$i][$email])) {
                    $username = strstr(trim($rows[$i][$email]), '@', true);
                    $username = str_replace('@', '', $username);
                    // Create user. If user exists, get user id
                    if (!$user = $DB->get_record('user', ['username' => $username])) {
                        $user = new \stdClass();
                        $user->username = $username;
                        $user->password = $this->random_password();
                        $user->auth = 'manual';
                        $user->firstname = trim($rows[$i][$first_name]);
                        $user->lastname = trim($rows[$i][$last_name]);
                        $user->email = trim($rows[$i][$email]);
                        $user->phone1 = trim($rows[$i][$phone1]);
                        $user->phone2 = trim($rows[$i][$phone2]);
                        $user_id = user_create_user($user);
                    } else {
                        $user_id = $user->id;
                    }

                    // insert organization contact
                    if (!$contact = $DB->get_record(TABLE_ORGANIZATION_CONTACT,
                        ['organizationid' => $organizationid, 'userid' => $user_id])) {
                        $contact = new \stdClass();
                        $contact->organizationid = $organizationid;
                        $contact->userid = $user_id;
                        $params->timecreated = time();
                        $params->timemodified = time();
                        $params->usermodified = $USER->id;

                        $DB->insert_record(TABLE_ORGANIZATION_CONTACT, $contact);
                    }

                }
                notification::success('Organization ' . $name . ' has been added.');
            } else {
                // Update organization phone. Must do it here because the first record for the organization
                // may not have the phone number available
                if (trim($rows[$i][$phone1])) {
                    $found->phone = trim($rows[$i][$phone1]);
                    $DB->update_record(TABLE_ORGANIZATION, $found);
                }
                // Update organization email. Must do it here because the first record for the organization
                // may not have the email available
                if (trim($rows[$i][$email])) {
                    $found->email = trim($rows[$i][$email]);
                    $DB->update_record(TABLE_ORGANIZATION, $found);
                }
                notification::WARNING('Organization ' . $name . ' already exists.');
            }
        }

        return true;
    }

    private function random_password()
    {
        global $CFG;

        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '1234567890';
        $symbols = '@!#&*#';
        $pass = array(); //remember to declare $pass as an array
        $alpha_length = strlen($alphabet) - 1; //put the length -1 in cache
        $numbers_length = strlen($numbers) - 1; //put the length -1 in cache
        $symbols_length = strlen($symbols) - 1; //put the length -1 in cache
        $numberic_position = rand(0, $CFG->minpasswordlength);
        $symbol_position = rand(0, 4);
        $uppercase_position = rand(5, 8);
        for ($i = 0; $i < $CFG->minpasswordlength; $i++) {
            $alpha_n = rand(0, $alpha_length);
            $numeric_n = rand(0, $numbers_length);
            $symbol_n = rand(0, $symbols_length);
            if ($i == $numberic_position) {
                $pass[] = $numbers[$numeric_n];
            } else if ($i == $symbol_position) {
                $pass[] = $symbols[$symbol_n];
            } else {
                $pass[] = $alphabet[$alpha_n];
            }
        }

        return implode($pass); //turn the array into a string
    }
}