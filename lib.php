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
 * Navigation menu
 * @desc this function will add menu & links.
 * 
 * @package    local_note
 * @copyright  2015 MoodleOfIndia
 * @author     shambhu kumar
 * @license    MoodleOfIndia {@web http://www.moodleofindia.com}
 */
function local_note_extend_navigation(global_navigation $navigation) {
    global $CFG;
    if (isloggedin()) {
        $node = $navigation->add(get_string('pluginname', 'local_note'));
        $node->add(get_string('addnote', 'local_note'), $CFG->wwwroot.'/local/note/add_note.php');
        $node->add(get_string('mynote', 'local_note'), $CFG->wwwroot.'/local/note/my_notes.php');
        $node->add(get_string('viewnote', 'local_note'), $CFG->wwwroot.'/local/note/view_all_note.php');
    }
}


function local_note_pluginfile($course, $birecord_or_cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB, $CFG;
    
    if ($context->contextlevel != CONTEXT_SYSTEM) {
        send_file_not_found();
    }

    // If block is in course context, then check if user has capability to access course.
    if ($context->get_course_context(false)) {
        //require_course_login($course);
    } else if ($CFG->forcelogin) {
        //require_login();
    } else {
        // Get parent context and see if user have proper permission.
        $parentcontext = $context->get_parent_context();
        if ($parentcontext->contextlevel === CONTEXT_COURSECAT) {
            // Check if category is visible and user can view this category.
            $category = $DB->get_record('course_categories', array('id' => $parentcontext->instanceid), '*', MUST_EXIST);
            if (!$category->visible) {
require_capability('moodle/category:viewhiddencategories', $parentcontext);
            }
        }
        // At this point there is no way to check SYSTEM or USER context, so ignoring it.
    }

    if ($filearea !== 'content') {
        send_file_not_found();
    }

    $fs = get_file_storage();

    $filename = array_pop($args);
    //$filepath = $args ? '/'.implode('/', $args).'/' : '/';
    $filepath = '/';
    $itemid  = $args['0']; //added by nihar -to get the item id

    if (!$file = $fs->get_file($context->id, 'local_note', 'content', $itemid, $filepath, $filename) or $file->is_directory()) {

        send_file_not_found();
    }

    session_get_instance()->write_close();

    send_stored_file($file, 60*60, 0, $forcedownload, $options);
}
