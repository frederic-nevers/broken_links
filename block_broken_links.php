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

        // TODO - add "only start at 2.30am" code. Also need to break out of the loop below after a certain time? 2 hours?
		$cronendtime = time() + 2 * 3600;	// TODO Make this an admin setting???

       	// Get the DB tables and fields that we're going to search. These will be in order of the oldest previous cron first.
        if (!$fields = $DB->get_records('block_broken_links_fields', array('active' => 1), 'lastcron ASC')) {
        	mtrace( "No active entries in block_broken_links_fields, so exiting." );
        	return true;
		}

        // This is the main loop for checking links.
        // We check each DB field in turn, looping through each record within that field in a sub-loop.
        // We start out from where we left off last time.
        foreach ($fields as $key => $field) {

			// Return all the records for this DB field as an associative array, where the key is the id field of the DB table
			// The lastcronid will only apply to the very first field we look at in this loop, as others will have lastcronid=0
			$sql = "SELECT id, $field->field FROM $field->table WHERE id > $field->lastcronid";
			$records = $DB->get_records_sql_menu($sql, array('lastcronid' => $field->lastcronid));

			foreach ($records as $id => $record) {			// Loop through each $record (string, not object) for this DB field
				$urls = $this->getlinks($record);			// Returns an array of URLs contained within the field (string)
				foreach ($urls as $url) {					// Loop through these URLs that have been found
					$broken = $this->checklink($url);		// Returns a 404-type code if the URL is broken
					if ($broken) {
						$entry->module = $field->modname;	// e.g. 'forum' or 'assign'
						$entry->urltocheck = $url;			// The broken URL itself
						list($entry->course, $entry->cmid) = $this->getmoduleinfo($field, $id);	// mdl_course.id and mdl_course_modules.id

						if (!$DB->record_exists('block_broken_links', $entry)) {
							$entry->timestamp = time();
							$entry->response = $broken;		// 404-type code
							$entry->ignoreurl = 0;
							$DB->insert_record('block_broken_links', $entry);
						}
					}
				}
				// Check if our 2 hours is up. If it is, break out of these loops and set the lastcronid to equal the record id we've reached
				if (time() > $cronendtime) {
					$field->lastcronid = $id;
					$DB->update_record('block_broken_links_fields', $field);
					break 2;	// Exit both $records and $fields loops
				}
			}
			// We've finished checking every record in this field, so we set lastcronid = 0 if it isn't already
			if ($field->lastcronid) {
				$field->lastcronid = 0;
			}
			// Also update the lastcron timestamp for this field to move it to the bottom of the waiting list.
			$field->lastcron = time();
			$DB->update_record('block_broken_links_fields', $field);
		}

        return true;
    }

	/**
	 * Returns an array of URLs contained within the string
	 *
	 * @param string $string - the contents of a database field
	 * @return array - an array of URLs as strings
	 */
    private function getlinks($string) {

    	// This is where our REGEXP goes - the way I imagine it, we take the $string and look for URLs
    	// Given that there may be more than one, we have to return our findings as an array - each element is simply a string URL
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

    	// This is where our CURL stuff goes - we take the $url and if it works, we return false.
    	// If it's broken, we return and integer 404 or 500 or whatever

    	return $code;
	}

	/**
	 * Returns the course id and the course module id associated with a particular record in a given table
	 *
	 * @param object $field - a record from table block_broken_links_fields that lets us know what table we're dealing with
	 * @param int $id - the id number of the record we're interested in from DB field '$field'
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
