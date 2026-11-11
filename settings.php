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
 * Completion Progress block settings
 *
 * @package   block_completion_progress_tgm
 * @copyright 2016 Michael de Raadt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

use block_completion_progress_tgm\defaults;

if ($ADMIN->fulltree) {
    $options = [10 => 10, 12 => 12, 14 => 14, 16 => 16, 18 => 18, 20 => 20];
    $settings->add(
        new admin_setting_configselect(
            'block_completion_progress_tgm/wrapafter',
            get_string('wrapafter', 'block_completion_progress_tgm'),
            '',
            defaults::WRAPAFTER,
            $options
        )
    );

    $options = [
        'squeeze' => get_string('config_squeeze', 'block_completion_progress_tgm'),
        'scroll' => get_string('config_scroll', 'block_completion_progress_tgm'),
        'wrap' => get_string('config_wrap', 'block_completion_progress_tgm'),
    ];
    $settings->add(
        new admin_setting_configselect(
            'block_completion_progress_tgm/defaultlongbars',
            get_string('defaultlongbars', 'block_completion_progress_tgm'),
            '',
            defaults::LONGBARS,
            $options
        )
    );

    $options = [
        'shortname' => get_string('shortname', 'block_completion_progress_tgm'),
        'fullname' => get_string('fullname', 'block_completion_progress_tgm'),
    ];
    $settings->add(
        new admin_setting_configselect(
            'block_completion_progress_tgm/coursenametoshow',
            get_string('coursenametoshow', 'block_completion_progress_tgm'),
            '',
            defaults::COURSENAMETOSHOW,
            $options
        )
    );

    $coloursetting = new admin_setting_configcolourpicker(
        'block_completion_progress_tgm/completed_colour',
        get_string('completed_colour_title', 'block_completion_progress_tgm'),
        get_string('completed_colour_descr', 'block_completion_progress_tgm'),
        get_string('completed_colour', 'block_completion_progress_tgm'),
        null
    );
    $coloursetting->set_updatedcallback('block_completion_progress_tgm\helpers::increment_cache_value');
    $settings->add($coloursetting);

    $coloursetting = new admin_setting_configcolourpicker(
        'block_completion_progress_tgm/submittednotcomplete_colour',
        get_string('submittednotcomplete_colour_title', 'block_completion_progress_tgm'),
        get_string('submittednotcomplete_colour_descr', 'block_completion_progress_tgm'),
        get_string('submittednotcomplete_colour', 'block_completion_progress_tgm'),
        null
    );
    $coloursetting->set_updatedcallback('block_completion_progress_tgm\helpers::increment_cache_value');
    $settings->add($coloursetting);

    $coloursetting = new admin_setting_configcolourpicker(
        'block_completion_progress_tgm/notCompleted_colour',
        get_string('notCompleted_colour_title', 'block_completion_progress_tgm'),
        get_string('notCompleted_colour_descr', 'block_completion_progress_tgm'),
        get_string('notCompleted_colour', 'block_completion_progress_tgm'),
        null
    );
    $coloursetting->set_updatedcallback('block_completion_progress_tgm\helpers::increment_cache_value');
    $settings->add($coloursetting);

    $coloursetting = new admin_setting_configcolourpicker(
        'block_completion_progress_tgm/futureNotCompleted_colour',
        get_string('futureNotCompleted_colour_title', 'block_completion_progress_tgm'),
        get_string('futureNotCompleted_colour_descr', 'block_completion_progress_tgm'),
        get_string('futureNotCompleted_colour', 'block_completion_progress_tgm'),
        null
    );
    $coloursetting->set_updatedcallback('block_completion_progress_tgm\helpers::increment_cache_value');
    $settings->add($coloursetting);

    $coloursetting = new admin_setting_configcolourpicker(
        'block_completion_progress_tgm/unavailable_colour',
        get_string('unavailable_colour_title', 'block_completion_progress_tgm'),
        get_string('unavailable_colour_descr', 'block_completion_progress_tgm'),
        get_string('unavailable_colour', 'block_completion_progress_tgm'),
        null
    );
    $coloursetting->set_updatedcallback('block_completion_progress_tgm\helpers::increment_cache_value');
    $settings->add($coloursetting);

    $settings->add(
        new admin_setting_configcheckbox(
            'block_completion_progress_tgm/showinactive',
            get_string('showinactive', 'block_completion_progress_tgm'),
            '',
            defaults::SHOWINACTIVE
        )
    );
    $settings->add(
        new admin_setting_configcheckbox(
            'block_completion_progress_tgm/showlastincourse',
            get_string('showlastincourse', 'block_completion_progress_tgm'),
            '',
            defaults::SHOWLASTINCOURSE
        )
    );
    $settings->add(
        new admin_setting_configcheckbox(
            'block_completion_progress_tgm/forceiconsinbar',
            get_string('forceiconsinbar', 'block_completion_progress_tgm'),
            '',
            defaults::FORCEICONSINBAR
        )
    );
    $settings->add(
        new admin_setting_configduration(
            'block_completion_progress_tgm/overviewcachetime',
            get_string('overviewcachetime', 'block_completion_progress_tgm'),
            '',
            defaults::OVERVIEWCACHETIME,
            HOURSECS
        )
    );
}
