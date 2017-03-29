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
require_once('../../config.php');

$action = optional_param('action', 'notes', PARAM_ALPHANUM);
$url = new moodle_url('/local/obf/courseuserbadges.php',array());

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');

$content = $OUTPUT->header();

switch ($action) {
    case 'badges':
        require_capability('local/obf:seeparticipantbadges', $context);
        $participants = get_enrolled_users($context, 'local/obf:earnbadge', 0, 'u.*', null, 0, 0, true);
        $content .= $PAGE->get_renderer('local_obf')->render_course_participants($courseid, $participants);
    break;
    case 'history':
        require_capability('local/obf:viewhistory', $context);
        $relatedevents = obf_issue_event::get_events_in_course($courseid, $DB);
        $client = new obf_client();
        $content .= $PAGE->get_renderer('local_obf')->print_badge_info_history($client, null, $context, 0, $relatedevents);
    break;
    case 'notes':
        //$notes = $DB->get_records('note');

        $notes = [];
        $content .= $PAGE->get_renderer('local_note')->print_notes($notes);
    break;
}

$content .= $OUTPUT->footer();

echo $content;
