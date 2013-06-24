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
 * Strings for component 'block_broken_links', language 'en'
 *
 * @package    block_broken_links
 * @copyright  Frederic Nevers <fredericnevers@gmail.com>
 * @copyright  Keith Wilson <keith@keith.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Site wide settings
$string['headerconfig'] = 'Configuration section header';
$string['descconfig'] = 'The following settings will affect every \'Broken links\' block on your Moodle installation. Choose carefully.';

//Ignore internal link site wide setting
$string['labelinternal_links'] = 'Internal links';
$string['descinternal_links'] = 'Should internal links be ignored and never be checked?';

//Ignored domains site wide setting
$string['nameignored_domains'] = 'Ignore domains';
$string['titleignored_domains'] = 'The URLs from the domains above will never be checked. Separate each domain with a comma, as shown in the example.';
$string['descignored_domains'] = 'example.com, somemoodle.com, ';

//Modules to check site wide setting
$string['namemodules'] = 'Activities and resources to check';
$string['titlemodules'] = 'Select the parts of Moodle you wish to check for broken links.';

//Days when cron should be run
$string['namecrondays'] = 'Check for broken links on';
$string['titlecrondays'] = 'Select the days when broken links should be checked.';

//Time at which cron should be run
$string['titlecrontime'] = 'Check for broken links at';
$string['desccrontime'] = 'Checking for broken links may take a long time and use up CPU. Choose a time when your site does not receive a lot of traffic.';

//Cron duration (in hours)
$string['desccronduration'] = 'Duration in hours. The system will stop checking for broken links after the number of hours selected.';

// Instance configuration strings
$string['blocktitle'] = 'Broken links';

// Permission strings
$string['broken_links:addinstance'] = 'Add a Broken links block';
$string['broken_links:myaddinstance'] = 'Add a Broken links block to my moodle';
$string['broken_links:view'] = 'View the Broken links block';

// General strings
$string['pluginname'] = 'Broken links';
