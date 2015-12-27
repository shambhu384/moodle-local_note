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
 * Add Note page 
 * @desc this page will crate a web form for note.
 * 
 * @package    local_note
 * @copyright  2015 MoodleOfIndia
 * @author     shambhu kumar
 * @license    MoodleOfIndia {@web http://www.moodleofindia.com}
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_login(0, false);
require_once('locallib.php');
require_once('classes/note_form.php');
$PAGE->set_url(new moodle_url('/local/note/add_note.php', array()));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout(get_layout());
$PAGE->requires->jquery();
$PAGE->set_title(get_string('addnote', 'local_note'));
$PAGE->set_heading(get_string('addnote', 'local_note'));
$noteform = new \note_form();
$message = false;
if ($noteform->is_cancelled()) {
    redirect(new moodle_url($CFG->wwwroot));
} else if ($data = $noteform->get_data()) {
    $data->createdtime = time();
    $data->publisherid = $USER->id;
    if ($DB->insert_record('cli_note', $data)) {
        $message = \html_writer::div(get_string("notecreated", "local_note"), 'alert alert-success');
        $context = \context_system::instance();
        file_save_draft_area_files($data->attachment, $context->id, 'local_note', 'content', $data->attachment, array('subdirs' => 0, 'maxbytes' => 5000000, 'maxfiles' => 1));
        $noteform = null;
    }
}
echo $OUTPUT->header();
echo html_writer::start_div('row');
echo html_writer::start_div('span12 noteform');
if ($message) {
    echo $message;
}
if ($noteform != null) {
    echo \html_writer::tag('p', get_string('addnote', 'local_note'), ['class' => 'lead']);
    $noteform->display();
}
echo html_writer::end_div();
echo html_writer::end_div();
echo $OUTPUT->footer();
