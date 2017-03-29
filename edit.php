<?php
// This file is part of MoodleofIndia - http://moodleofindia.com/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Edit Note page
 *
 * @package    local_note
 * @author     shambhu kumar
 * @license    learningpage.in
 */

require ('../../config.php');
require ('locallib.php');

// Prevent guest access
require_login(0, false);

$context = context_user::instance($USER->id);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$id = optional_param('id', -1, PARAM_INT);

$form = new note_form(null, compact('id'));
if($id == -1) {
    // Add new note
    $PAGE->set_title(get_string('compose', 'local_note'));
    $PAGE->set_heading(get_string('compose', 'local_note'));
    $PAGE->set_url($CFG->wwwroot . '/local/note/add_note.php');
} else {
    $PAGE->set_title(get_string('edit', 'local_note'));
    $PAGE->set_heading(get_string('edit', 'local_note'));
    $PAGE->set_url($CFG->wwwroot . '/local/note/add_note.php');
    $note = $DB->get_record('cli_note', array('id' => $id));
    $form->set_data($note);
}

//require_capability('local/note:edit', $context);

if ($form->is_cancelled()) {
    redirect(new moodle_url($CFG->wwwroot));
} else if ($data = $form->get_data()) {
    if($data->id == -1) {

        $DB->insert_record('note', $data);
    } else {
    
        file_save_draft_area_files(
            $data->attachment,
            $context->id,
            'local_note',
            'content',
            $data->attachment,
            array(
                'subdirs' => 0,
                'maxbytes' => 5000000,
                'maxfiles' => 10
            )
        );
    }

    redirect($CFG->wwwroot.'/local/note/view.php');
}

// Start making page
echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
