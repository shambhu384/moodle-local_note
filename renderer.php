<?php
// This file is part of Moodle - http://moodle.org/
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
 * Admin settings search form
 *
 * @package    admin
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * HTML output renderer for local_obf-plugin
 * 
 * @package    local_obf
 * @copyright  2013-2015, Discendum Oy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_note_renderer extends plugin_renderer_base {

    public function print_notes($backpacks) {
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
       $content = '';
        $table = new html_table();

        $table->head = array(get_string('backpackprovidershortname', 'local_obf'), get_string('backpackproviderfullname', 'local_obf'),
            get_string('backpackproviderurl', 'local_obf'), get_string('backpackprovideremailconfigureable', 'local_obf'), get_string('backpackprovideractions', 'local_obf'));

        foreach($backpacks as $backpack) {
            $row = new html_table_row();
            $editurl = new moodle_url('/local/obf/backpackconfig.php', array('action' => 'edit', 'id' => $backpack->get_provider()));
            $links = html_writer::link($editurl, get_string('edit'));
            $actionscell = new html_table_cell($links);
            $row->cells = array(
                $backpack->get_providershortname(),
                $backpack->get_providerfullname(),
                $backpack->get_apiurl(),
                $backpack->requires_email_verification() ? get_string('yes') : '',
                $actionscell
            );
            $table->data[] = $row;
        }
        $content .= html_writer::table($table);
        $createurl = new moodle_url('/local/obf/backpackconfig.php', array('action' => 'create'));
        $content .= html_writer::div(
            html_writer::link($createurl, get_string('create'), array('class' => 'btn btn-default'))
            ,
            'pull-right');
        return $content;
    }
    
    public function print_my_notes($backpacks) {
        $content = '';
    }


    public function render_note($backpacks) {
        $content = '';
        $table = new html_table();

        $table->head = array(get_string('backpackprovidershortname', 'local_obf'), get_string('backpackproviderfullname', 'local_obf'),
            get_string('backpackproviderurl', 'local_obf'), get_string('backpackprovideremailconfigureable', 'local_obf'), get_string('backpackprovideractions', 'local_obf'));

        foreach($backpacks as $backpack) {
            $row = new html_table_row();
            $editurl = new moodle_url('/local/obf/backpackconfig.php', array('action' => 'edit', 'id' => $backpack->get_provider()));
            $links = html_writer::link($editurl, get_string('edit'));
            $actionscell = new html_table_cell($links);
            $row->cells = array(
                $backpack->get_providershortname(),
                $backpack->get_providerfullname(),
                $backpack->get_apiurl(),
                $backpack->requires_email_verification() ? get_string('yes') : '',
                $actionscell
            );
            $table->data[] = $row;
        }
        $content .= html_writer::table($table);
        $createurl = new moodle_url('/local/obf/backpackconfig.php', array('action' => 'create'));
        $content .= html_writer::div(
            html_writer::link($createurl, get_string('create'), array('class' => 'btn btn-default'))
            ,
            'pull-right');
        return $content;
    }
}
