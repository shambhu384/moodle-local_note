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
 * Note_form class 
 * @desc Note_form class create a web form to create a note.
 * 
 * @package    local_note
 * @copyright  2015 MoodleOfIndia
 * @author     shambhu kumar
 * @license    MoodleOfIndia {@web http://www.moodleofindia.com}
 */

require_once("{$CFG->libdir}/formslib.php");
require_once("{$CFG->libdir}/enrollib.php");
require_once("note.php");

class note_form extends moodleform {
    private $noteid;
    public function __construct($noteid = null) {
        parent::__construct();
        $this->noteid = $noteid;
    }
    public function definition() {
        global $DB, $USER;
        $form = $this->_form;
                
        $selectdata = enrol_get_all_users_courses($USER->id, true, ['id','fullname']);
        $coursearray = null;
        $coursearray[null] = '-- SELECT --';
        foreach ($selectdata as $val) {
            $coursearray[$val->id] = $val->fullname;
        }
        $form->addElement('select', 'courseid', get_string('courseselect', 'local_note'), $coursearray);
        $form->setType('courseid', PARAM_RAW);
        $form->addRule('courseid', get_string('err_courseselect', 'local_note'), 'required', null, 'server', false, false);
        $form->addElement('text', 'title', get_string('title', 'local_note'));
        $form->setType('title', PARAM_RAW);
        
        $form->addElement('htmleditor', 'content', get_string("content", "local_note"), 'wrap="virtual" rows="3" cols="20"');
        $form->setType('content', PARAM_RAW);
        $form->addElement('filemanager', 'attachment', get_string('attachment', 'local_note'), null,
            array('maxbytes' => '2097152', 'accepted_types' => '*'));
        $form->addElement('text', 'label', get_string('label', 'local_note'));
        $form->setType('label', PARAM_RAW);
        $form->addElement('select', 'status', get_string("status", "local_note"), array('1' => 'Active', '0' => 'In-active'));
        $form->setType('status', PARAM_RAW);
        $form->addElement('hidden', 'id', $this->noteid);
        $form->setType('id', PARAM_RAW);
        $this->add_action_buttons();
    }

    public function validation($data, $files) {
        $errors = array();
        if (empty($data['courseid'])) {
            $errors['courseselect'] = get_string('err_courseselect', 'local_note');
        }
        return $errors;
    }
}

class delete_note extends moodleform {
      
    public function __construct() {
        parent::__construct();
    }
    public function definition() {
        $form = $this->_form;              
        $form->addElement('hidden', 'id','');
        $form->setType('id', PARAM_RAW);
        $this->add_action_buttons();
    }   
}