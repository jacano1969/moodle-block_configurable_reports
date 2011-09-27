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

// Based on Custom SQL Reports Plugin
// See http://moodle.org/mod/data/view.php?d=13&rid=2884

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/blocks/configurable_reports/components/customsql/form.php');

class drilldownsql_form extends customsql_form {
 function definition() {
        global $CFG;

        $mform =& $this->_form;

        $mform->addElement('static', 'note', '', get_string('drilldowninstructions', 'block_configurable_reports'));

        parent::definition();
    }

    function validation($data, $files) {
        global $CFG, $db, $USER;

        //$errors = parent::validation($data, $files);
        $errors = array();

        $sql = stripslashes($data['querysql']);
		$sql = trim($sql);

        // Simple test to avoid evil stuff in the SQL.
        if (preg_match('/\b(ALTER|CREATE|DELETE|DROP|GRANT|INSERT|INTO|TRUNCATE|UPDATE)\b/i', $sql)) {
            $errors['querysql'] = get_string('notallowedwords', 'block_configurable_reports');

        // Do not allow any semicolons.
        } else if (strpos($sql, ';') !== false) {
            $errors['querysql'] = get_string('nosemicolon', 'report_customsql');

        // Make sure prefix is prefix_, not explicit.
        } else if ($CFG->prefix != '' && preg_match('/\b' . $CFG->prefix . '\w+/i', $sql)) {
            $errors['querysql'] = get_string('noexplicitprefix', 'block_configurable_reports', $CFG->prefix);

        // Now try running the SQL, and ensure it runs without errors.
        } else {

			$sqls = $this->_customdata['reportclass']->prepare_sql($sql);
            if (!is_array($sqls)) {
                $sqls = array($sqls);
            }
            foreach ($sqls as $sql) {
                $rs = $this->_customdata['reportclass']->execute_query($sql, 2);
                if (!$rs) {
                    $errors['querysql'] = get_string('queryfailed', 'block_configurable_reports', $db->ErrorMsg());
                    break;
                } else if (!empty($data['singlerow'])) {
                    if (rs_EOF($rs)) {
                        $errors['querysql'] = get_string('norowsreturned', 'block_configurable_reports');
                        break;
                    }
                }

                if ($rs) {
                    rs_close($rs);
                }
            }
        }

        return $errors;
    }
}

?>