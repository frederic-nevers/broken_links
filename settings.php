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

$settings->add(new admin_setting_heading('sampleheader',
                                         get_string('headerconfig', 'block_broken_links'),
                                         get_string('descconfig', 'block_broken_links')));

//Single checkbox. Determines whether internal links should be checked
$settings->add(new admin_setting_configcheckbox('broken_links/internal_links',
                                                get_string('labelinternal_links', 'block_broken_links'),
                                                get_string('descinternal_links', 'block_broken_links'),
                                                '1'));

//Textbox with no formatting. Determines which domains should not be checked for broken URL's. Does not strip HTML tags. Output is saved as is in config_plugins
$settings->add(new admin_setting_configtextarea('broken_links/ignored_domains',
                                            get_string('nameignored_domains', 'block_broken_links'),
                                            get_string('titleignored_domains', 'block_broken_links'),
                                            get_string('descignored_domains', 'block_broken_links'),
                                            PARAM_RAW, 50, 10));

//Group of checkboxes. Determines the parts of Moodle for which broken URL's are checked. The output is saved in config_plugins in CSV format
$settings->add(new admin_setting_configmulticheckbox('broken_links/modules',
													get_string('namemodules','block_broken_links'),
													get_string('titlemodules','block_broken_links'),
													array(
													'assign'		=> 1,
													'assignment'	=> 0,
													'book'			=> 1,
													'forum' 		=> 1,
													'glossary'		=> 1,
													'label'			=> 1,
													'page'			=> 1,
													'url'			=> 1,
													'wiki'			=> 0),
													array(
													'assign'		=> get_string('textinstructions', 'mod_assign') . ' (' . get_string('modulename', 'mod_assign') . ')',
													'assignment'	=> get_string('description', 'mod_assignment') . ' (' . get_string('modulename', 'mod_assignment') . ')',
													'book'			=> get_string('modulenameplural', 'mod_book'),
													'forum'			=> get_string('forumposts', 'mod_forum'),
													'glossary'		=> get_string('modulenameplural', 'mod_glossary'),
													'label'			=> get_string('labeltext', 'mod_label'),
													'page'			=> get_string('modulenameplural', 'mod_page'),
													'url'			=> get_string('modulenameplural', 'mod_url'),
													'wiki'			=> get_string('modulenameplural', 'mod_wiki'),																			)));

//Group of checkboxes. Determines the days when cron should be run. The output is saved in 0111101 format in config_plugins
$settings->add(new admin_setting_configmulticheckbox2('broken_links/crondays',
													get_string('namecrondays','block_broken_links'),
													get_string('titlecrondays','block_broken_links'),
													array(
													'sunday'	=> 1,
													'monday' 	=> 1,
													'tuesday'	=> 1,
													'wednesday'	=> 1,
													'thursday'	=> 1,
													'friday'	=> 1,
													'saturday'	=> 1),
													array(
													'sunday'	=> get_string('sunday', 'core_calendar'),
													'monday'	=> get_string('monday', 'core_calendar'),
													'tuesday'	=> get_string('tuesday', 'core_calendar'),
													'wednesday'	=> get_string('wednesday', 'core_calendar'),
													'thursday'	=> get_string('thursday', 'core_calendar'),
													'friday'	=> get_string('friday', 'core_calendar'),
													'saturday'	=> get_string('saturday', 'core_calendar'),
													)));

//Time picker. Determines the time when cron should be run. The output is saved in 2 values in config_plugins; hourcrontime & minutecrontime
$settings->add(new admin_setting_configtime('broken_links/hourcrontime', 'minutecrontime',
											get_string('titlecrontime','block_broken_links'),
											get_string('desccrontime','block_broken_links'),
											array('h' => 2, 'm' => 30)));