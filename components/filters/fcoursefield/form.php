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

/** Configurable Reports
  * A Moodle block for creating customizable reports
  * @package blocks
  * @author: Juan leyva <http://www.twitter.com/jleyvadelgado>
  * @date: 2009
  */ 

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');

class fcoursefield_form extends moodleform {
    function definition() {
        global $course, $CFG, $db;

        $mform =& $this->_form;

        $mform->addElement('header', '', get_string('fcoursefield','block_configurable_reports'), '');

		$this->_customdata['compclass']->add_form_elements($mform,$this); 
		
		$columns = $db->MetaColumns($CFG->prefix . 'course');
		
		$coursecolumns = array();
		foreach($columns as $c)
			$coursecolumns[$c->name] = $c->name;
			
		unset($coursecolumns['password']);
		unset($coursecolumns['sesskey']);
			
        $mform->addElement('select', 'field', get_string('field','block_configurable_reports'), $coursecolumns);
		
       
        // buttons
        $this->add_action_buttons(true, get_string('add'));

    }

}

?>