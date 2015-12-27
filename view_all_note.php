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
 * View all note page 
 * @desc this page will all notes.
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
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout(get_layout());
$PAGE->set_title(get_string('viewallnote', 'local_note'));
$PAGE->set_heading(get_string('viewallnote', 'local_note'));
$PAGE->set_url($CFG->wwwroot . '/local/note/view_all_note.php');
echo $OUTPUT->header();
$note = new \moi\note($DB, $USER->id,$CFG);
echo html_writer::start_div('row');
echo html_writer::start_div('span12');
echo $note->get_all_notes();
echo html_writer::end_div();
echo html_writer::end_div();
echo $OUTPUT->footer();