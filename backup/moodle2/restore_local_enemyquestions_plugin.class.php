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
 * Grid Information
 *
 * @package    local_enemyquestions
 * @version    1.0
 * @copyright  &copy; 2015 Ray Morris <ray.morris@teex.tamu.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Restore plugin class that provides the necessary information
 * needed to restore enemyquestions
 */

class restore_local_enemyquestions_plugin extends restore_local_plugin {

    /**
     * Returns the paths to be handled by the plugin at course level.
     */
    protected function define_question_plugin_structure() {

        $paths = array();

        $elename = 'plugin_local_enemyquestions_question'; // This defines the postfix of 'process_*' below.
        $elepath = $this->get_pathfor('/');
        $paths[] = new restore_path_element($elename, $elepath);
        return $paths; // And we return the interesting paths.
    }


    /**
     * Process the enemyquestion element.
     */
    public function process_plugin_local_enemyquestions_question($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        unset($data->id);

        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = (bool) $this->get_mappingid('question_created', $oldquestionid);

        $data->questiona = $this->get_mappingid('question', $data->questiona);
        $data->questionb = $this->get_mappingid('question', $data->questionb);

        if ($data->questiona && $data->questionb) {
            $sql = 'SELECT * FROM {enemyquestions} WHERE (questiona=:qa1 AND questionb=:qb1) OR ((questiona=:qb2 AND questionb=:qa2))';
            $params = array('qa1' => $data->questiona, 'qb1' => $data->questionb, 'qa2' => $data->questiona, 'qb2' => $data->questionb);
            if(!$DB->record_exists_sql($sql, $params)) {
                $DB->insert_record('enemyquestions', $data);
            }
        } else {
            $this->set_mapping('enemyquestions', $this->get_old_parentid('question'), $this->get_new_parentid('question'));
        }
    }
}