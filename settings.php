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

$settings->add(new admin_setting_configcheckbox('broken_links/internal_links',
                                                get_string('labelinternal_links', 'block_broken_links'),
                                                get_string('descinternal_links', 'block_broken_links'),
                                                '1'));
                                                
$settings->add(new admin_setting_configtextarea('broken_links/ignored_domains',
                                            get_string('nameignored_domains', 'block_broken_links'),
                                            get_string('titleignored_domains', 'block_broken_links'),
                                            get_string('descignored_domains', 'block_broken_links'),
                                            PARAM_RAW, 100, 20));
                                            
                                                