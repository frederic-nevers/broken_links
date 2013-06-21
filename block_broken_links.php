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
 * broken_links block caps.
 *
 * @package    block_broken_links
 * @copyright  Frederic Nevers <fredericnevers@gmail.com>
 * @copyright  Keith Wilson <keith@keith.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_broken_links extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_broken_links');
    }

    function get_content() {
        global $CFG, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
        $this->content->text = '';

        // user/index.php expect course context, so get one if page has module context.
        $currentcontext = $this->page->context->get_course_context(false);

        // Only display the block to users with permission to view it.
        if (!has_capability('block/broken_links:view', $currentcontext)) {
            return $this->content;
		}

        if (! empty($this->config->text)) {
            $this->content->text = $this->config->text;
        }

        if (empty($currentcontext)) {
            return $this->content;
        }

        if ($this->page->course->id == SITEID) {
            $this->context->text .= "site context";
        }

        global $COURSE, $DB;
        $modinfo = get_fast_modinfo($COURSE);					// Get details of the modules in this course

        // Display the database records of broken links for this course
        $links = $DB->get_records('block_broken_links', array('course' => $COURSE->id, 'ignoreurl' => false));
        foreach ($links as $link) {
        	// First test to see if the course moodule still exists - if it doesn't, delete this broken_links DB record.
        	if (!array_key_exists($link->cmid, $modinfo->cms)) {
        		$DB->delete_records('block_broken_links', $link);	// KSW TODO is this the place to be deleting reconrds?
        		continue;
			}
			$mod = $modinfo->cms[$link->cmid];					// Retrieve the module object
			$o = html_writer::empty_tag('img', array('src' => $mod->get_icon_url(),
                'class' => 'iconsmall activityicon', 'alt' => $mod->modfullname));   	// Display module icon
			$o .= $mod->name;									// Display module name
			$o .= '';											// Action icons
			$this->content->text .= html_writer::tag('div', $o, array('class' => 'broken_link'));	// Wrap each link in a div
		}


        return $this->content;
    }

    // my moodle can only have SITEID and it's redundant here, so take it away
    public function applicable_formats() {
        return array('all' => false,
                     'site' => true,
                     'site-index' => true,
                     'course-view' => true,
                     'course-view-social' => false,
                     'mod' => true,
                     'mod-quiz' => false);
    }

    public function instance_allow_multiple() {
          return false;
    }

    function has_config() {return true;}

    public function cron() {
        mtrace( "Starting cron script for block broken_links" );

        // TODO - add "only start at 2.30am" code. Also need to break out of the loop below after a certain time?

        if (!$fields = $DB->get_records('block_broken_links_fields', array('active' => 1), 'lastcron ASC')) {
        	mtrace( "No active entries in block_broken_links_fields, so exiting." );
        	return true;
		}

		// Work out where we left off last time this cron ran.
		$lastfield = 0;
		$lastfieldid = 0;
		foreach ($fields as $key => $field) {
			if ($field->lastcron > 0) {
				$lastfield = $key;
				$lastfieldid = $field->lastcron;
			}
		}
		// Rotate array of DB fields so the foreach will start at the right place
		if ($lastfield) {
			$fields = array_merge(array_slice($fields, $lastfield), array_slice($fields, 0, $lastfield));
		}

        // This is the main loop for checking links.
        // We check each DB field in turn, looping through each record within that field in a sub-loop.
        // We start out from where we left off last time.
        foreach ($fields as $key => $field) {
			$sql = "SELECT id, $field->field FROM $field->table WHERE id > $field->lastcronid";
			$records = $DB->get_records_sql_menu($sql, array('lastcronid' => $field->lastcronid));

			foreach ($records as $record) {					// Loop through each record for this DB field
				$urls = $this->getlinks($record);			// Returns an array of URLs contained within the field
				foreach ($urls as $url) {					// Loop through these URLs
					$broken = $this->checklink($url);		// Returns a 404-type code if the URL is broken
					if ($broken) {
						$entry->module = $field->modname;
						$entry->urltocheck = $url;
						list($entry->course, $entry->cmid) = $this->getmoduleinfo($field, $record);

						if (!$DB->record_exists('block_broken_links', $entry)) {
							$entry->timestamp = time();
							$entry->response = $broken;
							$entry->ignoreurl = 0;
							$DB->insert_record('block_broken_links', $entry);
						}
					}
				}
			}
		}

        return true;
    }

	/**
	 * Returns an array of URLs contained within the string
	 *
	 * @param string $field - the contents of a database field
	 * @return array - an array of URLs as strings
	 */
    private function getlinks($field) {

    	// This is where our REGEXP goes
		$urls = array();

		return $urls;
	}

	/**
	 * Returns a 404-type code if the URL is broken, otherwise returns false
	 *
	 * @param string $url - the URL to be checked
	 * @return mixed
	 */
    private function checklink($url) {

    	$code = false;

    	return $code;
	}

	/**
	 * Returns the course id and the course module id associated with a particular record in a given table
	 *
	 * @param object $field - a record from table block_broken_links_fields that lets us know what table we're dealing with
	 * @param int $id - the id number of the record we're interested in
	 * @return array
	 */
    private function getmoduleinfo($field, $id) {

    	// Get the module id number to simplify the sql statements below
    	$modid = $DB->get_field('modules', 'id', array ('name' => $field->modname), MUST_EXIST);

    	// First handle the easy cases - where this field is the standard intro field of the main module table
    	if ($field->table == $field->modname && $field->field == 'intro') {
    		$course = $DB->get_field($field->table, 'course', array ('id' => $id), MUST_EXIST);
    		$cmid = $DB->get_field('course_modules', 'id', array ('instance' => $id, 'module' => $modid), MUST_EXIST);
    		return array($course, $cmid);
		}

    	// Now the non-standard cases
    	switch ($field->table) {

		    case "forum_posts":
    			$sql = "SELECT cm.id AS cmid, d.course FROM course_modules cm
    					  JOIN forum_discussions d ON d.forum = cm.instance
    				      JOIN forum_posts p ON p.discussion = d.id
    				     WHERE p.id = :id ";
		        break;

		    // TODO need more of these
		}

		$cminfo = $DB->get_record_sql($sql, array ('id' => $id, 'module' => $modid), MUST_EXIST);
        $course = $cminfo->course;
        $cmid = $cminfo->cmid;

    	return array($course, $cmid);
	}

}
