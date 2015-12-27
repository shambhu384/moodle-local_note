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
 * Note class is build for Manage Notes (Create/Update/Delete)
 * @desc Note class have one parameterized constructor to receive global 
 *       resources.
 * 
 * @package    local_note
 * @copyright  2015 MoodleOfIndia
 * @author     shambhu kumar
 * @license    MoodleOfIndia {@web http://www.moodleofindia.com}
 */

namespace moi;
require_once("note_form.php");
/**
 * Base class of note
 */
class note {

    private $db;
    private $userid;
    private $context;
    private $cfg;
    
    public function __construct($DB = null,  $userid = null,  $CFG = null,  $context = null) {
        $this->db = $DB;
        $this->userid = $userid;
        $this->context = $context;
        $this->cfg = $CFG;
    }

    public function delete($noteid) {
        if ($this->db->delete_records('cli_note', array('id' => $noteid))) {
            return true;
        } 
        return false;
    }
    public function get_all_notes() {
        echo \html_writer::tag('p', get_string('viewallnote', 'local_note'), ['class' => 'lead']);
        $courses = enrol_get_my_courses('id');
        $courselist = [];
        foreach ($courses as $key => $nouse) {
            $courselist[] = $key;
        }
        if(empty($courselist)) {
            return \html_writer::div(get_string("nonotesavailable",  "local_note"), 'alert alert-warning');
        }
        $sql ='select * from {cli_note} where courseid in('.implode(',', $courselist).') and status = 1 order by id DESC';
        $record = $this->db->get_records_sql($sql);
        if (!empty($record) && $record != null) {
            $table = new \html_table();            
            $table->head = (array) get_strings(['serial','author','date','course','title','content','attachment','label','active','action'],'local_note');
            $count = 1;
            foreach ($record as $key => $row) {
                    $status = $row->status == true ? '<span class="label label-success">' . get_string('active', 'local_note') . '</span>' :
                    '<span class="label label-warning">' . get_string('inactive', 'local_note') . '</span>';
                $action = '<i class="fa fa-lock" title="Access denied"></i>';
                $user = $this->db->get_record('user',['id' => $row->publisherid], 'firstname, lastname');
                $author = $user->firstname.' '.$user->lastname;
                if ($this->userid == $row->publisherid) {
                    $action = \html_writer::link(new \moodle_url($this->cfg->wwwroot . '/local/note/edit_note.php?id=' . $row->id), '<i class="fa fa-edit"></i> ', ['title' => 'Edit note']);
                    $action .= \html_writer::link(new \moodle_url($this->cfg->wwwroot . '/local/note/delete_note.php?id=' . $row->id), ' <i class="fa fa-trash"></i>', ['title' => 'Delete note']);
                }
                $course = $this->db->get_record('course',array('id'=>$row->courseid),'fullname');
                $table->data[] = array(
                    $count++,
                    $author,
                    date('j M Y', $row->createdtime),
                    \html_writer::link(new \moodle_url($this->cfg->wwwroot.'/course/view.php',['id'=>$row->courseid]), $course->fullname),
                    '<a href="view_note.php?id=' . $row->id . '">' . $row->title . '</a>',
                    strip_tags(substr($row->content, 0, 20)) . '&nbsp;<a href="view_note.php?id=' . $row->id . '">' .
                    get_string('more', 'local_note') . '</a>',
                    \html_writer::link(new \moodle_url($this->get_attachment($row->attachment)['url']), $this->get_attachment($row->attachment)['filename'], ['download'=>'download']),
                    $row->label,
                    $status,
                    $action
                );
            }
            return \html_writer::table($table);
        } else {
            return \html_writer::div(get_string("nonotesavailable",  "local_note"), 'alert alert-warning');
        }
    }

    public function get_note($noteid) {
        $record = $this->db->get_record("cli_note", array('id' => $noteid));
        $publisherid = (int) $record->publisherid;
        $name = $this->db->get_record_sql("select concat(firstname, ' ', lastname) as name from {user} where "
            . "id=$publisherid")->name;
        $viewarray = [
                'title' => $record->title,
                'name' => $name,
                'content' => $record->content,
                'publisherid' => $record->publisherid,
                'datetime' => $record->createdtime,
                'attchfile' => $this->get_attachment($record->attachment)['url'],
                'attchfilename' => $this->get_attachment($record->attachment)['filename']
            ];
        return $viewarray;
    }

    public function author_authenticate($noteid) {
        $record = $this->db->record_exists('cli_note', array('id' => $noteid, 'publisherid' => $this->userid));
        return $record;
    }
    public function get_attachment($draftid) {
        $fs = get_file_storage();
        $context = \context_system::instance();
        if (!$files = $fs->get_area_files($context->id,  'local_note',  'content',  $draftid,  'id DESC',  false)) {
            return false;
        }
        $file = reset($files);
        $url = \moodle_url::make_pluginfile_url($context->id,'local_note','content',$draftid, $file->get_filepath(), $file->get_filename(), true);
        return ['url' => $url, 'filename' => $file->get_filename()];
    }
    
    public function get_all_my_notes() {
        echo \html_writer::tag('p', get_string('mynotes', 'local_note'), ['class' => 'lead']);
        $record = $this->db->get_records("cli_note", array('publisherid' => $this->userid), 'id desc');
        if ($record != null) {
            $table = new \html_table();
            $table->head = (array) get_strings(['serial', 'date', 'course', 'title', 'content', 'attachment', 'label', 'status', 'action'], 'local_note');
            $count = 1;
            foreach ($record as $row) {
                $status = $row->status == true ? '<span class="label label-success">' . get_string('active', 'local_note') . '</span>' :
                    '<span class="label label-warning">' . get_string('inactive', 'local_note') . '</span>';
                $action = '<i class="fa fa-lock" title="Access denied"></i>';
                if ($this->userid == $row->publisherid) {
                    $action = \html_writer::link(new \moodle_url($this->cfg->wwwroot . '/local/note/edit_note.php?id=' . $row->id), '<i class="fa fa-edit"></i> ', ['title' => 'Edit note']);
                    $action .= \html_writer::link(new \moodle_url($this->cfg->wwwroot . '/local/note/delete_note.php?id=' . $row->id), ' <i class="fa fa-trash"></i>', ['title' => 'Delete note']);
                }
                $course = $this->db->get_record('course',array('id'=>$row->courseid),'fullname');
                $table->data[] = array(
                    $count++,                    
                    date('j M Y', $row->createdtime),
                    \html_writer::link(new \moodle_url($this->cfg->wwwroot.'/course/view.php',['id'=>$row->courseid]), $course->fullname),
                    '<a href="view_note.php?id=' . $row->id . '">' . $row->title . '</a>',
                    strip_tags(substr($row->content, 0, 20)) . '&nbsp;<a href="view_note.php?id=' . $row->id . '">' .
                    get_string('more', 'local_note') . '</a>',
                    \html_writer::link(new \moodle_url($this->get_attachment($row->attachment)['url']), $this->get_attachment($row->attachment)['filename'], ['download'=>'download']),
                    $row->label,
                    $status,
                    $action
                );
            }
            echo \html_writer::start_div('div');
            echo \html_writer::table($table);
            echo \html_writer::end_div('div');
        }
        else {
            echo \html_writer::div(get_string("nonotesavailable", "local_note"), 'alert alert-warning');
        }
    }
    /**
     * 
     * @param type $noteid
     * @return boolean
     */
    public function haspermission($noteid) {      
        $note = $this->db->get_record('cli_note',['id' =>$noteid]);
        $context = \context_course::instance($note->courseid);
        if(is_enrolled($context)){
            return true;
        }
        return false;
    }
}