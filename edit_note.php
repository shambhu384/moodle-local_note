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
 * @desc this page will edit notes.
 * 
 * @package    local_note
 * @copyright  2015 MoodleOfIndia
 * @author     shambhu kumar
 * @license    MoodleOfIndia {@web http://www.moodleofindia.com}
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_login(0,false);
require_once('locallib.php');
require_once('classes/note.php');
$noteid = required_param('id', PARAM_INT);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout(get_layout());
$PAGE->set_title(get_string('editnote', 'local_note'));
$PAGE->set_heading(get_string('editnote', 'local_note'));
$PAGE->set_url($CFG->wwwroot . '/local/note/add_note.php');
$note = new \moi\note($DB, $USER->id);
if(!$note->author_authenticate($noteid,false)){
    throw new moodle_exception('nopermissiontoshow');
}
$noteform = new \note_form($noteid);
$data = $DB->get_record('cli_note', array('id' => $noteid));
$noteform->set_data($data);
$message = false;
if ($noteform->is_cancelled()) {
    redirect(new moodle_url($CFG->wwwroot.'/local/note/my_notes.php'));
} else if ($data = $noteform->get_data()) {
    $data->modifiedtime = time();
    $data->publisherid = $USER->id;
    if ($DB->update_record('cli_note', $data)) {
        $context = \context_system::instance();
        file_save_draft_area_files($data->attachment, $context->id, 'local_note', 'content',
        $data->attachment, array('subdirs' => 0, 'maxbytes' => 5000000, 'maxfiles' => 1)); 
        $message = \html_writer::div(get_string("noteupdated",  "local_note"), 'alert alert-success');
        $noteform= null;                
    }
}       
echo $OUTPUT->header();
echo html_writer::start_div('row');
echo html_writer::start_div('span12 noteform');
if($message) {
    echo $message;
}
if ($noteform != null){
    echo \html_writer::tag('p', get_string('editnote', 'local_note') , ['class' =>'lead']);
    $noteform->display();
}
echo html_writer::end_div();
echo html_writer::end_div();
echo $OUTPUT->footer();