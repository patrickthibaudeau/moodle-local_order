<?php

require_once("../../../config.php");

require_once($CFG->dirroot . "/local/order/classes/forms/import_campus.php");

use core\notification;
use local_order\base;
use local_order\import;


global $CFG, $OUTPUT, $USER, $PAGE, $DB, $SITE;

$id = optional_param('id', 0, PARAM_INT);
$err = optional_param('err', '', PARAM_TEXT);

$context = CONTEXT_SYSTEM::instance();

require_login(1, false);

base::page(
    '/local/order/import/campus.php',
    get_string('campus_import', 'local_order'),
    get_string('campus_import', 'local_order'),
    $context
);

if (!$id) {
    $mform = new \local_order\import_campus();

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
        $success = $mform->save_file('file', $full_path);

        $IMPORT = new import($full_path);
        $first_row = $IMPORT->get_first_row();
        $clean_columns = $IMPORT->clean_column_names();
        $rows = $IMPORT->get_rows();
        //Remove file
        unlink($full_path);
        echo $OUTPUT->header();
        if ($IMPORT->campus($first_row, $rows)) {
            echo '<a href="' . $CFG->wwwroot . '/local/order/index.php" class="btn btn-primary">' .
                get_string('home', 'local_order') . '</a>';
        }
        echo $OUTPUT->footer();

        die;
    } else {
        echo $OUTPUT->header();

        if ($err) {
            notification::error("The file is missing column $err");
        }
        $mform->display();

        echo $OUTPUT->footer();
        die;
    }
}



?>