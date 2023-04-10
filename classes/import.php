<?php

namespace local_order;

require_once($CFG->libdir . '/phpspreadsheet/vendor/autoload.php');

use core\notification;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use local_order\inventories;

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
        if (!in_array('code', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=name');
        }
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
        foreach ($columns as $key => $column_name) {
            switch ($column_name) {
                case 'code':
                    $code = $key;
                    break;
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
                case 'ccemail':
                    $ccemail = $key;
                    break;
                case 'phone1':
                    $phone1 = $key;
                    break;
                case 'phone2':
                    $phone2 = $key;
                    break;
                case 'costcentre':
                    $costcentre = $key;
                    break;
                case 'fund':
                    $fund = $key;
                    break;
                case 'activitycode':
                    $activitycode = $key;
                    break;
            }
        }

        // Import campus data if it doesn;t already exists.
        for ($i = 1; $i < count($rows) - 1; $i++) {
//            $fields = explode(' - ', $rows[$i][$organization]);
//            if (count($fields) == 2) {
//                $code = trim($fields[0]);
//                $name = trim($fields[1]);
//            } else {
//                $code = '';
//                $name = trim($fields[0]);
//            }


            if (!$found = $DB->get_record(TABLE_ORGANIZATION, ['name' => $name])) {
                // Insert into table
                $params = new \stdClass();
                $params->name = trim($rows[$i][$organization]);
                $params->code = trim($rows[$i][$code]);;
                $params->phone = trim($rows[$i][$phone1]);
                $params->email = trim($rows[$i][$email]);
                $params->ccemail = trim($rows[$i][$ccemail]);
                $params->costcentre = trim($rows[$i][$costcentre]);
                $params->fund = trim($rows[$i][$fund]);
                $params->activitycode = trim($rows[$i][$activitycode]);
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
                        $contact->primarycontact = 1;
                        $contact->timecreated = time();
                        $contact->timemodified = time();
                        $contact->usermodified = $USER->id;

                        $DB->insert_record(TABLE_ORGANIZATION_CONTACT, $contact);
                    }

                }
                notification::success('Organization ' . $params->name . ' has been added.');
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
                notification::WARNING('Organization ' . $found->name . ' already exists.');
            }
        }

        return true;
    }

    /**
     * @param $columns array
     * @param $rows array
     * @param $type int 1 = AV, 2 = Catering, 3 = Furnishing
     * @param $timezone string
     * @return void
     */
    public function event($columns, $rows, $type, $timezone)
    {
        global $CFG, $DB, $USER;

        $INVENTORIES = new inventories();
        // get all inventory items
        $inventory_items = $INVENTORIES->get_records_by_category($type);

        // Make sure the columns exist
        if (!in_array('Registrant ID', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=Registrant&nbsp;ID');
        }
        if (!in_array('organizationid', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=Assoc');
        }
        if (!in_array('Event Title', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=Request&nbsp;title');
        }
        if (!in_array('Request Date', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=Date');
        }
        if (!in_array('Event start time', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=Start');
        }
        if (!in_array('Event end time', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=End');
        }
        if (!in_array('Allocated room', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=Allocated&nbsp;room');
        }
        if (!in_array('Catering', $columns)) {
            redirect($CFG->wwwroot . '/local/order/import/index.php?err=This&nbsp;room&nbsp;request&nbsp;requires&nbsp;a&nbsp;catering&nbsp;order?');
        }
        // Set the proper column key
        $registration_id = -1;
        $organizationid = -1;
        $title = -1;
        $date = -1;
        $start = -1;
        $end = -1;
        $room = -1;
        $catering = -1;
        $event_type = -1;
        $attendance = -1;
        $setup_type = -1;
        $setup_notes = -1;
        // Set the proper key value for the columns
        foreach ($columns as $key => $name) {
            switch ($name) {
                case 'Registrant ID':
                    $registration_id = $key;
                    break;
                case 'organizationid':
                    $organizationid = $key;
                    break;
                case 'Event Title':
                    $title = $key;
                    break;
                case 'Request Date':
                    $date = $key;
                    break;
                case 'Event start time':
                    $start = $key;
                    break;
                case 'Event end time':
                    $end = $key;
                    break;
                case 'Allocated room':
                    $room = $key;
                    break;
                case 'Catering':
                    $catering = $key;
                    break;
                case 'Event-type':
                    $event_type = $key;
                    break;
                case 'Exp. Attn':
                    $attendance = $key;
                    break;
                case 'Set-up':
                    $setup_type = $key;
                    break;
                case 'setup-notes':
                    $setup_notes = $key;
                    break;
            }
        }

        // get keys for inventory items (from inventory table) and create dynamic variables
        foreach ($inventory_items as $items) {
            foreach ($columns as $key => $name) {
                if (trim($items->name) == trim($name)) {
                    $variable = '_' . $items->code;
                    $$variable = $key;
                }
            }
        }

        // Store Current timezone
        $current_timezone = date_default_timezone_get();
        // Set timezone based on value from form
        date_default_timezone_set($timezone);
ob_start();

;
        // Loop through all events
        for ($i = 1; $i < count($rows) - 1; $i++) {
            // Prepare all event data
            // Get organization id
            $code = trim($rows[$i][$organizationid]);
            // get organization
            $organization = $DB->get_record(TABLE_ORGANIZATION, ['code' => $code]);

            // Convert date to array
            $date_array = explode('/', $rows[$i][$date]);

            // Reset date format as YY-mm-dd
            $new_date_format = $date_array[2] . '-' . $date_array[1] . '-' . $date_array[0];
            // Add time to both start date and end date
            $start_string = $new_date_format . ' ' . trim($rows[$i][$start]) . ':00';
            $end_string = $new_date_format . ' ' . trim($rows[$i][$end]) . ':00';

            // Convert to timestamp
            $start_time = strtotime($start_string);
            $end_time = strtotime($end_string);

            // Get catering if exists
            if ($catering != -1) {
                if (trim($rows[$i][$catering]) == 'Yes') {
                    $catering_data = true;
                } else {
                    $catering_data = false;
                }
            }

            // Get event type if exists
            $event_type_data = '';
            if ($event_type != -1) {
                $event_type_data = trim($rows[$i][$event_type]);
            }

            // Get attendance if exists
            $attendance_data = '';
            if ($attendance != -1) {
                $attendance_data = trim($rows[$i][$attendance]);
            }

            // Get setup type if exists
            $setup_type_data = '';
            if ($setup_type != -1) {
                $setup_type_data = trim($rows[$i][$setup_type]);
            }

            // Get setup type if exists
            $setup_notes_data = '';
            if ($setup_notes != -1) {
                $setup_notes_data = trim($rows[$i][$setup_notes]);
            }

            // Room if exists
            $roomid = 0;
            $room_name = '';
            if ($room != -1) {
                $room_title = trim($rows[$i][$room]);
                $room_array = explode('-', $room_title);
                // Get rid of building name
                unset($room_array[0]);
                // Get acronym and room number
                $room_acrm_number = explode(' ', $room_array[1]);
                $number_of_fields = count($room_acrm_number);
                $building_shortname = $room_acrm_number[0];
                if ($number_of_fields > 2) {
                    // Iterate through room numbers starting at 1
                    for($x = 1; $x < $number_of_fields; $x++) {
                        // Add room number to building name
                        $room_name .= ' ' . $room_acrm_number[$x];
                    }
                } else {
                    $room_name = $room_acrm_number[1];
                }
                print_object($building_shortname . ' ' . $room_name);
                // Get room record from order_room_basic table
                if ($room_data = $DB->get_record(TABLE_ROOM_BASIC, [
                    'building_shortname' => trim($building_shortname),
                    'name' => trim($room_name)])) {
                    $roomid = $room_data->id;
                }
            }

            // create event object
            $event = new \stdClass();
            $event->organizationid = $organization->id;
            $event->name = trim($rows[$i][$title]);
            $event->code = trim($rows[$i][$registration_id]);
            $event->starttime = $start_time;
            $event->endtime = $end_time;
            $event->roomid = $roomid;
            $event->eventtype = $event_type_data;
            $event->attendance = $attendance_data;
            $event->setuptype = $setup_type_data;
            $event->setupnotes = $setup_notes_data;


            // Check to see if the event already exists
            if (!$found = $DB->get_record(TABLE_EVENT, ['code' => trim($rows[$i][$registration_id])])) {
                $event_id = $DB->insert_record(TABLE_EVENT, $event);
            } else {
                $event_id = $found->id;
                $event->id = $event_id;
                $DB->update_record(TABLE_EVENT, $event);
            }
            ob_flush();
            flush();
// Event inventory will be imported in a different way
            // Check to see if event inventory category exists
//            if (!$event_inventory_category = $DB->get_record(TABLE_EVENT_INVENTORY_CATEGORY,
//                ['eventid' => $event_id, 'inventorycategoryid' => $type])) {
//                // Get inventory category
//                $inventory_category = $DB->get_record(TABLE_INVENTORY_CATEGORY, ['id' => $type]);
//                // Get admin notes
//                $admin_notes = '';
//                $notes = '';
//                foreach ($columns as $key => $name) {
//                    if ($name == 'Admin notes - ' . strtolower($inventory_category->name)) {
//                        $admin_notes = trim($rows[$i][$key]);
//                    }
//
//                    if ($type == 1 && $name == 'Custom Audio Visual Comments - Questions') {
//                        $notes = trim($rows[$i][$key]);
//                    }
//
//                    if ($type == 3 && $name == 'Custom Furnishing Comments - Questions') {
//                        $notes = trim($rows[$i][$key]);
//                    }
//                }
//                // Create event inventory category object and capture the id
//                $eic_params = new \stdClass();
//                $eic_params->eventid = $event_id;
//                $eic_params->inventorycategoryid = $type;
//                $eic_params->name = $inventory_category->name;
//                $eic_params->notes = $notes;
//                $eic_params->adminnotes = $admin_notes;
//                $eic_params->timecreated = time();
//                $eic_params->timemodified = time();
//                $eic_params->usermodified = $USER->id;
//
//                $event_inventory_category_id = $DB->insert_record(TABLE_EVENT_INVENTORY_CATEGORY, $eic_params);
//            } else {
//                $event_inventory_category_id = $event_inventory_category->id;
//            }
//
//            // Finally import all inventory items
//            foreach ($inventory_items as $items) {
//                $variable = '_' . $items->code;
//                $event_inventory = new \stdClass();
//                if (trim($rows[$i][$$variable])) {
//                    $event_inventory_array = [
//                        'eventcategoryid' => $event_inventory_category_id,
//                        'inventoryid' => $items->id
//                    ];
//                    // Add inventory item if data exists for it.
//                    if (!$event_inventory_item = $DB->get_record(TABLE_EVENT_INVENTORY, $event_inventory_array)) {
//                        $event_inventory->eventcategoryid = $event_inventory_category_id;
//                        $event_inventory->inventoryid = $items->id;
//                        $event_inventory->name = $items->name;
//                        $event_inventory->description = trim($rows[$i][$$variable]);
//                        $event_inventory->timecreated = time();
//                        $event_inventory->timemodified = time();
//                        $event_inventory->usermodified = $USER->id;
//                        $DB->insert_record(TABLE_EVENT_INVENTORY, $event_inventory);
//                    }
//                }
//                unset($event_inventory);
//            }
        }
        ob_clean();
        // reset timezone to the default time zone.
        date_default_timezone_set($current_timezone);
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