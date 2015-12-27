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
 * View note 
 * @desc this page will view on author of notes
 * 
 * @package    local_note
 * @copyright  2015 MoodleOfIndia
 * @author     shambhu kumar
 * @license    MoodleOfIndia {@web http://www.moodleofindia.com}
 */
require_once('./../../config.php');
require_once('classes/note.php');
require_once('locallib.php');
require_login(0,false);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout(get_layout());
$PAGE->set_title(get_string('viewnote', 'local_note'));
$PAGE->set_heading(get_string('viewnote', 'local_note'));
$PAGE->set_url($CFG->wwwroot . '/local/note/view_all_note.php');

$note = new \moi\note($DB, $USER->id);
$id = optional_param('id', false, PARAM_INT);
$row = $note->get_note($id);
$PAGE->navbar->add($row['title']);

echo $OUTPUT->header();
echo html_writer::start_div('row');
if(!$note->haspermission($id)){
    throw new moodle_exception('nopermissiontoshow');
}
$content = html_writer::tag('p', $row['title'], ['class' => 'lead']);
$link = html_writer::link(new moodle_url($CFG->wwwroot.'/user/profile.php?id='.$row['publisherid']), $row['name']);
$content .= html_writer::tag('p', get_string('author', 'local_note').' '.$link);
$content .= html_writer::tag('p', get_string('postedon', 'local_note').userdate($row['datetime'], '%d %b %Y'), ['class' => 'pull-left']);
if (isset($row['attchfilename'])) {
    $content .= html_writer::tag('p', get_string('attachment', 'local_note').' <i class="icon  icon-download-alt"></i><a href="'.$row['attchfile'].'">Download</a>', ['class' => 'pull-right']);
}
$content .= html_writer::empty_tag('br');
$content .= html_writer::empty_tag('hr');
$content .= html_writer::div($row['content']);
echo html_writer::start_div('span12');
echo $content;
echo html_writer::end_div();
echo html_writer::end_div();
echo $OUTPUT->footer();