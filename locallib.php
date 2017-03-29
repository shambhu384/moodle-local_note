<?php

require_once("{$CFG->libdir}/formslib.php");
require_once("{$CFG->libdir}/enrollib.php");

class note_form extends moodleform {

    public function definition() {
        $mform = $this->_form;

        $courses = enrol_get_my_courses();
        $options = array(0 => 'All Courses');
        foreach ($courses as $course) {
            $options[$course->id] = $course->fullname;
        }
        $title = ($this->_customdata['id'] == -1) ? 'compose' : 'edit';
        $mform->addElement('header', 'header', get_string($title, 'local_note'));

        $mform->addElement('text', 'title', get_string('title', 'local_note'));
        $mform->setType('title', PARAM_RAW);

        $mform->addElement('editor', 'content', get_string("content", "local_note"), 'wrap="virtual" rows="14" cols="20"');
        $mform->setType('content', PARAM_RAW);
        $mform->addElement('filemanager', 'attachment', get_string('attachment', 'local_note'), null,
            array('maxbytes' => '2097152', 'accepted_types' => '*'));

        $radioarray=array();
        $attributes = [];
        $radioarray[] = $mform->createElement('radio', 'yesno', '', get_string('private', 'local_note'), 1, $attributes);
        $radioarray[] = $mform->createElement('radio', 'yesno', '', get_string('public', 'local_note'), 0, $attributes);
        $mform->addGroup($radioarray, 'radioar', 'Privacy', array(' '), false);

        $availablefromgroup=array();
        $availablefromgroup[] =& $mform->createElement('select', 'course', '', $options);
        $availablefromgroup[] =& $mform->createElement('checkbox', 'availablefromenabled', '', 'enable');
        $mform->addGroup($availablefromgroup, 'availablefromgroup', 'Courses', ' ', false);
        $mform->disabledIf('availablefromgroup', 'availablefromenabled');

        $mform->addElement('select', 'status', get_string("status", "local_note"), array(
            get_string('draft', 'local_note'),
            get_string('publish', 'local_note')
        ));
        $mform->setType('status', PARAM_RAW);


        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_RAW);
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
