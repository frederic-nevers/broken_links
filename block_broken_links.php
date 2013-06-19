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

        // user/index.php expect course context, so get one if page has module context.
        $currentcontext = $this->page->context->get_course_context(false);

        if (! empty($this->config->text)) {
            $this->content->text = $this->config->text;
        }

        $this->content = '';
        if (empty($currentcontext)) {
            return $this->content;
        }

        if ($this->page->course->id == SITEID) {
            $this->context->text .= "site context";
        }

        if (! empty($this->config->text)) {
            $this->content->text .= $this->config->text;
        }

        global $COURSE, $DB;

        // Display the database records of broken links for this course
        $links = $DB->get_records('broken_links', array('course' => $COURSE->id, 'ignoreurl' => false));
        foreach ($links as $link) {
        	// First test to see if the course moodule still exists - if it doesn't, delete this broken_links DB record.
        	if (!$cm = get_coursemodule_from_id($link->module, $link->$cmid, $COURSE->id)) {
        		$DB->delete_records('broken_links', $link);		// KSW TODO is this the place to be deleting reconrds?
        		continue;
			}
			$o = ''; 											// Display module icon
			$o .= $cm->name;									// Display module name
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
            mtrace( "Hey, my cron script is running" );

                 // do something

                      return true;
    }
}
