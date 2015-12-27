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
require(dirname(dirname(dirname(__FILE__))).'/config.php');
require_login(0,false);
require_once('classes/note.php');
require_once('locallib.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout(get_layout());
$PAGE->set_title(get_string('deletenote', 'local_note'));
$PAGE->set_heading(get_string('deletenote', 'local_note'));
$PAGE->set_url($CFG->wwwroot . '/local/note/my_notes.php');
if(!$id = required_param('id', PARAM_INT)) {
    new moodle_exception('invalidparam');
}
$form = new delete_note();
$form->set_data((object)array('id'=>$id));
$message = false;
if ($form->is_cancelled()) {
    redirect(new moodle_url($CFG->wwwroot.'/local/note/my_notes.php'));
} else if ($data = $form->get_data()) {
    $note = new \moi\note($DB, $USER->id);
    if ($note->author_authenticate($data->id)) {
        if ($note->delete($data->id)) {
            $message = html_writer::div(get_string('notedeleted' ,'local_note'), 'alert alert-success');
            $form = null;
        }
    } else {
        $message = \html_writer::div(get_string('permisssiondenieddelete' ,'local_note'), 'alert alert-danger');
        $form = null;
    }    
}
echo $OUTPUT->header();
echo html_writer::start_div('row');
echo html_writer::start_div('span12');
if($message){
    echo $message;
}
if ($form != null) {
    echo html_writer::tag('p', get_string('confirmdelete', 'local_note') , ['class' =>'lead']);
    $form->display();
}
echo html_writer::end_div();
echo html_writer::end_div();
echo $OUTPUT->footer();