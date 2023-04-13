<?php

require_once("../../../config.php");

require_once($CFG->dirroot . "/local/order/classes/forms/import_form.php");

use core\notification;
use local_order\base;
use local_order\import;


global $CFG, $OUTPUT, $USER, $PAGE, $DB, $SITE;

$id = optional_param('id', 0, PARAM_TEXT);
$import = optional_param('import', 'campus', PARAM_TEXT);
$err = optional_param('err', '', PARAM_TEXT);

$context = CONTEXT_SYSTEM::instance();

require_login(1, false);

$PAGE->requires->js_call_amd('local_order/import', 'init');

base::page(
    '/local/order/import/index.php?import=' . $import,
    get_string('import', 'local_order'),
    get_string('import', 'local_order'),
    $context
);

if (!$id) {
    $mform = new \local_order\import_form();

    if ($mform->is_cancelled()) {
        //Handle form cancel operation, if cancel button is present on form
        redirect($CFG->wwwroot . '/local/order/index.php');
    } else if ($data = $mform->get_data()) {

        $path = $CFG->dataroot . '/temp/import/';
        if (!is_dir($path)) {
            mkdir($path);
        }

        $name = $mform->get_new_filename('file');
        $full_path = $path . "$name";
        $success = $mform->save_file('file', $full_path, true);


        $IMPORT = new import($full_path);
        $first_row = $IMPORT->get_first_row();
        $clean_columns = $IMPORT->clean_column_names();
        $rows = $IMPORT->get_rows();

        //Remove file
        unlink($full_path);
        echo $OUTPUT->header();
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_HUGE);
        switch ($data->import_type) {
            case 'campus':
                if ($IMPORT->campus($first_row, $rows)) {
                    echo '<a href="' . $CFG->wwwroot . '/local/order/import/index.php?import=building" class="btn btn-primary">' .
                        get_string('import', 'local_order') . '</a>';
                }
                break;
            case 'building':
                if ($IMPORT->building($first_row, $rows)) {
                    echo '<a href="' . $CFG->wwwroot . '/local/order/import/index.php?import=floor" class="btn btn-primary">' .
                        get_string('import', 'local_order') . '</a>';
                }
                break;
            case 'floor':
                if ($IMPORT->floor($first_row, $rows)) {
                    echo '<a href="' . $CFG->wwwroot . '/local/order/import/index.php?import=room_type" class="btn btn-primary">' .
                        get_string('import', 'local_order') . '</a>';
                }
                break;
            case 'room_type':
                if ($IMPORT->room_type($first_row, $rows)) {
                    echo '<a href="' . $CFG->wwwroot . '/local/order/import/index.php?import=room" class="btn btn-primary">' .
                        get_string('import', 'local_order') . '</a>';
                }
                break;
            case 'room':
                $IMPORT->room($first_row, $rows);
                    echo '<a href="' . $CFG->wwwroot . '/local/order/import/index.php?import=room" class="btn btn-primary">' .
                        get_string('import', 'local_order') . '</a>';

                break;
            case 'inventory':
                if ($data->inventory_category) {
                    $IMPORT->inventory($clean_columns, $data->inventory_category);
                    echo '<a href="' . $CFG->wwwroot . '/local/order/import/index.php?import=inventory" class="btn btn-primary">' .
                        get_string('import', 'local_order') . '</a>';
                } else {
                    echo '<div class="alert alert-danger mb-3">' . get_string('inventory_category_required', 'local_order') . '</div>';
                    echo '<a href="' . $CFG->wwwroot . '/local/order/import/index.php?import=inventory" class="btn btn-primary">' .
                        get_string('import', 'local_order') . '</a>';
                }
                break;
            case 'organization':
                    $IMPORT->organization($first_row, $rows);
                    echo '<a href="' . $CFG->wwwroot . '/local/order/import/index.php?import=organization" class="btn btn-primary">' .
                        get_string('import', 'local_order') . '</a>';
                break;
            case 'event':
                $IMPORT->event($first_row, $rows, $data->inventory_category, $data->timezone);
                echo '<a href="' . $CFG->wwwroot . '/local/order/import/index.php?import=event" class="btn btn-primary">' .
                    get_string('import', 'local_order') . '</a>';
                break;
            case 'event_inventory':
                $IMPORT->event_inventory($first_row, $rows, $data->inventory_category, $data->section);
                echo '<a href="' . $CFG->wwwroot . '/local/order/import/index.php?import=event_inventory" class="btn btn-primary">' .
                    get_string('import', 'local_order') . '</a>';
                break;
        }
        raise_memory_limit(MEMORY_STANDARD);
        echo $OUTPUT->footer();

        die;
    } else {
        echo $OUTPUT->header();
//**********************
//*** DISPLAY HEADER ***
//

        if ($err) {
            notification::error("The file is missing column $err");
        }
        $IMPORT = new import();
        $can_import = $IMPORT->can_import($import);

        if (!is_array($can_import)) {
            $mform->display();
        } else {
            notification::error(get_string('table_must_have_data', 'local_order')
                . ': ' . implode(', ', $can_import) . ' '
            . get_string('select_other_import', 'local_order'));

        }



//**********************
//*** DISPLAY FOOTER ***
//**********************
        echo $OUTPUT->footer();
        die;
    }
}

echo $OUTPUT->header();
//**********************
//*** DISPLAY HEADER ***
//
echo "<div class='row'>";
echo "<div class='col-md-4'>";
echo 'Stuff';
echo "</div>";
echo "</div>";
//**********************
//*** DISPLAY FOOTER ***
//**********************
echo $OUTPUT->footer();


?>