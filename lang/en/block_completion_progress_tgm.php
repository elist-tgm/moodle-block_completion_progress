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
 * Completion Progress block English language translation
 *
 * @package    block_completion_progress_tgm
 * @copyright  2016 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['completed_colour'] = '#6A9C35';
$string['completed_colour_descr'] = 'HTML Colour code for elements that have been completed';
$string['completed_colour_title'] = 'Completed colour';
$string['completion_not_enabled'] = 'Completion tracking is not enabled on this site.';
$string['completion_not_enabled_course'] = 'Completion tracking is not enabled in this course.';
$string['completion_progress_tgm:addinstance'] = 'Add a new Completion Progress block';
$string['completion_progress_tgm:myaddinstance'] = 'Add a Completion Progress block to My home page';
$string['completion_progress_tgm:overview'] = 'View course overview of Completion Progress for all students';
$string['completion_progress_tgm:showbar'] = 'Show the bar in the Completion Progress block';
$string['computeprogress'] = 'Calculating progress percentages';
$string['config_activitiesincluded'] = 'Activities included';
$string['config_activitycompletion'] = 'All activities with completion set';
$string['config_default_title'] = 'Completion Progress (TGM)';
$string['config_group'] = 'Visible only to group or grouping';
$string['config_header_monitored'] = 'Monitored';
$string['config_icons'] = 'Use icons in bar';
$string['config_longbars'] = 'How to present long bars';
$string['config_orderby'] = 'Order bar by';
$string['config_orderby_course_order'] = 'Ordering in course';
$string['config_orderby_due_time'] = 'Time using "{$a}" date';
$string['config_percentage'] = 'Show percentage to students';
$string['config_scroll'] = 'Scroll';
$string['config_selectactivities'] = 'Select activities';
$string['config_selectedactivities'] = 'Selected activities';
$string['config_showunavailable'] = 'Activities not (yet) accessible';
$string['config_showunavailable_help'] = '<p>Activities may be inaccessible for a student, e.g. because a restriction (prerequisite, date, grade, ...) is not yet met.</p><ul><li><b>Show as grey cells</b>: every such activity is shown as a grey cell, even if the restriction hides it entirely on the course page. All students see a bar of the same length and the percentage refers to all activities.</li><li><b>Original behaviour</b>: activities hidden entirely by a restriction are left out; greyed-out ones are shown in the normal colours.</li></ul><p>Activities hidden by the teacher are never shown.</p>';
$string['config_unavailable_grey'] = 'Show as grey cells';
$string['config_unavailable_hide'] = 'Original behaviour (leave out hidden ones)';
$string['config_unavailableskipgroups'] = 'Leave out activities for other groups';
$string['config_unavailableskipgroups_help'] = '<p>If enabled, an inaccessible activity is left out if it is restricted (by a mandatory "AND" condition) to a group or grouping the student does not belong to. Such activities are intended for other students and are not part of this student\'s workload.</p>';
$string['config_squeeze'] = 'Squeeze';
$string['config_title'] = 'Alternate title';
$string['config_wrap'] = 'Wrap';
$string['coursenametoshow'] = 'Course name to show on Dashboard';
$string['defaultlongbars'] = 'Default presentation for long bars';
$string['errorinvalidblockinstanceid'] = 'Inappropriate block instance id given.';
$string['forceiconsinbar'] = 'Force icons in the progress bar';
$string['fullname'] = 'Full course name';
$string['futureNotCompleted_colour'] = '#025187';
$string['futureNotCompleted_colour_descr'] = 'HTML colour code for future elements that have not yet been completed';
$string['futureNotCompleted_colour_title'] = 'Future not-completed colour';
$string['how_activitiesincluded_works'] = 'How including activities works';
$string['how_activitiesincluded_works_help'] = '<p>By default, all activities with Activity completion settings set are included in the bar.</p><p>You can also manually select activities to be included.</p>';
$string['how_group_works'] = 'How visible group works';
$string['how_group_works_help'] = '<p>Selecting a group or grouping will limit the display of the this block to that group or grouping only.</p>';
$string['how_longbars_works'] = 'How long bars are presented';
$string['how_longbars_works_help'] = '<p>When bars exceed a set length, how they can be presented in one of the following ways.</p><ul><li>Squeezed into one horizontal bar</li><li>Scrolling sideways to show overflowing bar segments</li><li>Wrapping to show all bar segments on multiple lines</li></ul><p>Note that when the bar is wrapped, the NOW indicator will not be shown.</p>';
$string['how_ordering_works'] = 'How ordering works';
$string['how_ordering_works_help'] = '<p>There are two ways to order activities in the Completion Progress block.</p><ul><li><em>Time using "Expect completion on" date</em> (default)<br />The expected completion dates of activities/resources are used to order the bar. Where activities/resources don\'t have an expected completion date, ordering in the course is used instead. When this option is used, the NOW indicator is shown.</li><li><em>Ordering in course</em><br />Activities/resources are presented in the same order as they are on the course page. When this option is used, expected completion dates are ignored. When this option is used, the NOW indicator is not shown.</li></ul>';
$string['how_selectactivities_works'] = 'How including activities works';
$string['how_selectactivities_works_help'] = '<p>To manually select activities to be include in the bar, ensure that "Activities included" is set to "selected activities".</p><p>Only activities with activity completion settings set can be included.</p><p>Hold the CTRL key to select multiple activities.</p>';
$string['indeterminate'] = '?';
$string['lastonline'] = 'Last in course';
$string['mouse_over_prompt'] = 'Mouse over or touch bar for info.';
$string['no_activities_config_message'] = 'There are no activities or resources with activity completion set or no activities or resources have been selected. Set activity completion on activities and resources then configure this block.';
$string['no_activities_message'] = 'No activities or resources are being monitored. Use config to set up monitoring.';
$string['no_blocks'] = 'No Completion Progress blocks are set up for your courses.';
$string['no_courses'] = "You are not enrolled in any courses. Only bars from enrolled courses will be shown.";
$string['no_visible_activities_message'] = 'None of the monitored activities are currently visible.';
$string['notCompleted_colour'] = '#C71C22';
$string['notCompleted_colour_descr'] = 'HTML colour code for current elements that have not yet been completed';
$string['notCompleted_colour_title'] = 'Not-completed colour';
$string['not_all_expected_set'] = 'Not all activities with completion have an "{$a}" date set.';
$string['now_indicator'] = 'NOW';
$string['overview'] = 'Overview of students';
$string['overviewcachetime'] = 'Time to cache computed percentages';
$string['pluginname'] = 'Completion Progress (TGM)';
$string['privacy:metadata:block_completion_progress_tgm:blockinstanceid'] = 'The Completion Progress block instance identifier the percentage relates to.';
$string['privacy:metadata:block_completion_progress_tgm:courseid'] = 'The course the percentage relates to.';
$string['privacy:metadata:block_completion_progress_tgm:percentage'] = 'The percentage of completion.';
$string['privacy:metadata:block_completion_progress_tgm:tableexplanation'] = 'The Completion Progress block caches completion percentages for sorting of the Overview table.';
$string['privacy:metadata:block_completion_progress_tgm:timemodified'] = 'The time at which the percentage was calculated.';
$string['privacy:metadata:block_completion_progress_tgm:userid'] = 'The user identifier to whom the percentage relates.';
$string['progress'] = 'Progress';
$string['progressbar'] = 'Completion Progress';
$string['progresscachetime'] = 'Calculated {$a} ago';
$string['selectitem'] = 'Select \'{$a}\'';
$string['shortname'] = 'Short course name';
$string['showallinfo'] = 'Show all info';
$string['showinactive'] = 'Show inactive students in Overview';
$string['showlastincourse'] = 'Show student\'s Last in course time in Overview';
$string['submitted'] = 'Submitted';
$string['submittednotcomplete_colour'] = '#FFCC00';
$string['submittednotcomplete_colour_descr'] = 'HTML colour code for elements that have been submitted, but are not yet complete';
$string['submittednotcomplete_colour_title'] = 'Submitted but not complete colour';
$string['time_expected'] = 'Expected';
$string['unavailable'] = 'Not yet available';
$string['unavailable_colour'] = '#B0B0B0';
$string['unavailable_colour_descr'] = 'HTML colour code for elements that are not (yet) accessible for the user';
$string['unavailable_colour_title'] = 'Not available colour';
$string['why_set_the_title'] = 'Why you might want to set the block instance title?';
$string['why_set_the_title_help'] = '<p>There can be multiple instances of the Completion Progress block. You may use different Completion Progress blocks to monitor different sets of activities or resources. For instance you could track progress in assignments in one block and quizzes in another. For this reason you can override the default title and set a more appropriate block title for each instance.</p>';
$string['why_show_precentage'] = 'Why show a progress percentage to students?';
$string['why_show_precentage_help'] = '<p>It is possible to show an overall percentage of progress to students.</p><p>This is calculated as the number of activities completed divided by the total number of activities in the bar.</p><p>The progress percentage appears until the student mouses over an item in the bar.</p>';
$string['why_use_icons'] = 'Why you might want to use icons?';
$string['why_use_icons_help'] = '<p>You may wish to add tick and cross icons in the Completion Progress to make this block more visually accessible for students with colour-blindness.</p><p>It may also make the meaning of the block clearer if you believe colours are not intuitive, either for cultural or personal reasons.</p>';
$string['wrapafter'] = 'When wrapping, limit rows to';
