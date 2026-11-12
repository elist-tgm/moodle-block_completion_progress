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
 * Completion Progress block.
 *
 * @package    block_completion_progress_tgm
 * @copyright  2016 Michael de Raadt
 * @copyright  2021 Jonathon Fowler <fowlerj@usq.edu.au>
 * @copyright  2026 Erhard List <elist@tgm.ac.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_completion_progress_tgm;

use stdClass;
use completion_info;
use context_course;
use coding_exception;

/**
 * Completion Progress.
 *
 * @package    block_completion_progress_tgm
 * @copyright  2016 Michael de Raadt
 * @copyright  2021 Jonathon Fowler <fowlerj@usq.edu.au>
 * @copyright  2026 Erhard List <elist@tgm.ac.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion_progress implements \renderable, \templatable {
    /**
     * Sort activities by course order.
     */
    const ORDERBY_COURSE = 'orderbycourse';

    /**
     * Sort activities by expected time order.
     */
    const ORDERBY_TIME = 'orderbytime';

    /**
     * Group activities by task type, course order within a group.
     */
    const ORDERBY_TASKTYPE = 'orderbytasktype';

    /**
     * Task type icon position in grouped mode: in each cell.
     */
    const TYPEICON_CELL = 'cell';

    /**
     * Task type icon position in grouped mode: as group header.
     */
    const TYPEICON_HEADER = 'header';

    /**
     * Task type icon position in grouped mode: header and cells.
     */
    const TYPEICON_BOTH = 'both';

    /**
     * Hide activities that are restricted and hidden entirely (original behaviour).
     */
    const UNAVAILABLE_HIDE = 'hide';

    /**
     * Show all (not yet) accessible activities as grey cells.
     */
    const UNAVAILABLE_GREY = 'grey';

    /**
     * The course.
     * @var object
     */
    protected $course;

    /**
     * The course context.
     * @var context_course
     */
    protected $context;

    /**
     * Completion info for the course.
     * @var completion_info
     */
    protected $completioninfo;

    /**
     * The user.
     * @var object
     */
    protected $user;

    /**
     * Block instance record.
     * @var stdClass
     */
    protected $blockinstance;

    /**
     * Block instance config.
     * @var stdClass
     */
    protected $blockconfig;

    /**
     * List of activities.
     * @var array cmid => obj
     */
    protected $activities = null;

    /**
     * List of visible activities.
     * @var array cmid => obj
     */
    protected $visibleactivities = null;

    /**
     * List of grade exclusions.
     * @var array of: [module-instance-userid, ...]
     */
    protected $exclusions = null;

    /**
     * List of submissions.
     * @var array of arrays: userid => [cmid => obj]
     */
    protected $submissions = null;

    /**
     * List of computed completions.
     * @var array of arrays: userid => [cmid => state]
     */
    protected $completions = null;

    /**
     * Whether exclusions have been loaded for all course users already.
     * @var bool
     */
    protected $exclusionsforall = false;

    /**
     * Whether submissions have been loaded for all course users already.
     * @var bool
     */
    protected $submissionsforall = false;

    /**
     * Whether completions have been loaded for all course users already.
     * @var bool
     */
    protected $completionsforall = false;

    /**
     * Simple bar mode (for overview).
     * @var bool
     */
    protected $simplebar = false;

    /**
     * Constructor.
     * @param object|int $courseorid
     */
    public function __construct($courseorid) {
        global $CFG;

        require_once($CFG->libdir . '/completionlib.php');

        if (is_object($courseorid)) {
            $this->course = $courseorid;
        } else {
            $this->course = get_course($courseorid);
        }
        $this->context = context_course::instance($this->course->id);
        $this->completioninfo = new completion_info($this->course);
    }

    /**
     * Specialise for a specific user.
     * @param stdClass $user containing minimum of core_user\fields::for_name()
     * @return self
     */
    public function for_user(stdClass $user): self {
        $this->user = $user;

        $this->load_exclusions();
        $this->load_submissions();
        $this->load_completions();
        $this->filter_visible_activities();

        return $this;
    }

    /**
     * Specialise for overview page use.
     * @return self
     */
    public function for_overview(): self {
        if ($this->user) {
            throw new coding_exception('cannot re-specialise for overview');
        }
        $this->user = null;
        $this->simplebar = true;

        $this->load_exclusions();
        $this->load_submissions();
        $this->load_completions();

        return $this;
    }

    /**
     * Compute and cache completion percentages for overview use.
     * @param callable $progresscallback receives a percentage
     * @return void
     */
    public function compute_overview_percentages($progresscallback = null): void {
        global $DB;
        if ($this->user) {
            throw new coding_exception('cannot compute overview percentages when specialised for a user');
        } else if (!$this->completionsforall) {
            throw new coding_exception('cannot compute overview percentages until completions are loaded');
        }

        if (is_callable($progresscallback)) {
            call_user_func($progresscallback, 0);
        }

        $clock = \core\di::get(\core\clock::class);

        $numdone = 0;
        $numcompletions = count($this->completions);
        $cachetime = get_config('block_completion_progress_tgm', 'overviewcachetime') ?: defaults::OVERVIEWCACHETIME;
        foreach ($this->completions as $userid => $completions) {
            $trans = $DB->start_delegated_transaction();
            $rec = [
                'blockinstanceid' => $this->blockinstance->id,
                'courseid' => $this->course->id,
                'userid' => $userid,
            ];
            $rec = $DB->get_record('block_completion_tgm', $rec) ?: (object)$rec;

            if (!empty($rec->timemodified) && $clock->time() - $rec->timemodified < $cachetime) {
                $trans->allow_commit();
                continue;
            }

            if (count($completions) == 0) {
                $rec->percentage = null;
            } else {
                $this->for_user((object)['id' => $userid]);
                if (empty($this->visibleactivities)) {
                    $rec->percentage = null;
                } else {
                    $completecount = 0;
                    foreach ($completions as $cmid => $complete) {
                        if (!isset($this->visibleactivities[$cmid])) {
                            continue;
                        }
                        if ($complete == COMPLETION_COMPLETE || $complete == COMPLETION_COMPLETE_PASS) {
                            $completecount++;
                        }
                    }
                    $rec->percentage = (int)round(100 * $completecount / count($this->visibleactivities));
                }
            }
            $rec->timemodified = $clock->time();

            if (empty($rec->id)) {
                $rec->id = $DB->insert_record('block_completion_tgm', $rec);
            } else {
                $DB->update_record('block_completion_tgm', $rec);
            }
            $trans->allow_commit();

            $numdone++;
            if (is_callable($progresscallback)) {
                call_user_func($progresscallback, 100 * $numdone / $numcompletions);
            }
        }

        if (is_callable($progresscallback)) {
            call_user_func($progresscallback, 100);
        }
    }

    /**
     * Specialise for a particular block instance.
     * @param stdClass $instance Instance record.
     * @param boolean $selectedonly Whether to filter by configured selected items.
     * @return self
     */
    public function for_block_instance(stdClass $instance, $selectedonly = true): self {
        if ($this->blockinstance) {
            throw new coding_exception('cannot re-specialise for a different block instance');
        }
        $this->blockinstance = $instance;
        $this->blockconfig = (object)(array)unserialize(base64_decode($instance->configdata ?? ''));

        $this->load_activities($selectedonly);
        $this->filter_visible_activities();

        return $this;
    }

    /**
     * Return the course object.
     * @return object
     */
    public function get_course(): stdClass {
        return $this->course;
    }

    /**
     * Return the course object.
     * @return context_course
     */
    public function get_context(): context_course {
        return $this->context;
    }

    /**
     * Return the completion info object.
     * @return completion_info
     */
    public function get_completion_info(): completion_info {
        return $this->completioninfo;
    }

    /**
     * Return the user.
     * @return stdClass
     */
    public function get_user(): ?stdClass {
        return $this->user;
    }

    /**
     * Return the simple bar mode.
     * @return boolean
     */
    public function is_simple_bar(): bool {
        return $this->simplebar;
    }

    /**
     * Check whether any activities are available.
     * @return boolean
     */
    public function has_activities(): bool {
        if ($this->activities === null) {
            throw new coding_exception('activities not loaded until for_block_instance() is called');
        }
        return !empty($this->activities);
    }

    /**
     * Return the activities in presentation order.
     * @param string|null $orderoverride
     * @return array
     */
    public function get_activities($orderoverride = null): array {
        if ($this->activities === null) {
            throw new coding_exception('activities not loaded until for_block_instance() is called');
        }
        $order = $orderoverride ?? $this->blockconfig->orderby ?? self::ORDERBY_COURSE;
        usort($this->activities, [$this, 'sorter_' . $order]);
        return $this->activities;
    }

    /**
     * Check whether any visible activities are available.
     * @return boolean
     */
    public function has_visible_activities(): bool {
        if ($this->visibleactivities === null) {
            throw new coding_exception('visible activities not computed until for_block_instance() is called');
        }
        return !empty($this->visibleactivities);
    }

    /**
     * Return the activities visible to the user in presentation order.
     * @param string|null $orderoverride
     * @return array
     */
    public function get_visible_activities($orderoverride = null): array {
        if ($this->visibleactivities === null) {
            throw new coding_exception('visible activities not computed until for_block_instance() is called');
        }
        $order = $orderoverride ?? $this->blockconfig->orderby ?? self::ORDERBY_COURSE;
        usort($this->visibleactivities, [$this, 'sorter_' . $order]);
        return $this->visibleactivities;
    }

    /**
     * Return the exclusions.
     * @return array of modname-modinstance-userid formatted items
     */
    public function get_exclusions(): array {
        return $this->exclusions;
    }

    /**
     * Get block instance.
     * @return stdClass|null
     */
    public function get_block_instance(): ?stdClass {
        return $this->blockinstance;
    }

    /**
     * Get block configuration.
     * @return stdClass
     */
    public function get_block_config(): stdClass {
        return $this->blockconfig;
    }

    /**
     * Get user activity submissions.
     * @return array cmid => info
     */
    public function get_submissions(): array {
        if ($this->submissions === null) {
            throw new coding_exception('submissions not computed until for_user() or for_overview() is called');
        }
        if ($this->user) {
            return $this->submissions[$this->user->id] ?? [];
        } else {
            throw new coding_exception('unimplemented');
        }
    }

    /**
     * Get user activity completion states.
     * @return array cmid => status
     */
    public function get_completions() {
        if ($this->completions === null) {
            throw new coding_exception('completions not computed until for_user() or for_overview() is called');
        }
        if ($this->user) {
            // Filter to visible activities and fill in gaps.
            $completions = $this->completions[$this->user->id] ?? [];
            $ret = [];
            foreach ($this->visibleactivities as $activity) {
                $ret[$activity->id] = $completions[$activity->id] ?? COMPLETION_INCOMPLETE;
            }
            return $ret;
        } else {
            throw new coding_exception('unimplemented');
        }
    }

    /**
     * Calculates an overall percentage of progress.
     * @return integer  Progress value as a percentage
     */
    public function get_percentage(): ?int {
        $completions = $this->get_completions();
        if (count($completions) == 0) {
            return null;
        }

        $completecount = 0;
        foreach ($completions as $complete) {
            if ($complete == COMPLETION_COMPLETE || $complete == COMPLETION_COMPLETE_PASS) {
                $completecount++;
            }
        }

        return (int)round(100 * $completecount / count($this->visibleactivities));
    }

    /**
     * Used to compare two activity entries based on order on course page.
     *
     * @param object $a
     * @param object $b
     * @return integer
     */
    private function sorter_orderbycourse($a, $b): int {
        if ($a->section != $b->section) {
            return $a->section <=> $b->section;
        } else {
            return strnatcasecmp($a->position, $b->position);
        }
    }

    /**
     * Used to compare two activity entries based their expected completion times
     *
     * @param object $a
     * @param object $b
     * @return integer
     */
    private function sorter_orderbytime($a, $b): int {
        if ($a->expected != 0 && $b->expected != 0 && $a->expected != $b->expected) {
            return $a->expected <=> $b->expected;
        } else if ($a->expected != 0 && $b->expected == 0) {
            return -1;
        } else if ($a->expected == 0 && $b->expected != 0) {
            return 1;
        } else {
            return $this->sorter_orderbycourse($a, $b);
        }
    }


    /**
     * Used to compare two activity entries by task type (definition order, untyped last),
     * then by order on the course page.
     *
     * @param object $a
     * @param object $b
     * @return integer
     */
    private function sorter_orderbytasktype($a, $b): int {
        $ta = isset($a->tasktype) ? $a->tasktype->order : PHP_INT_MAX;
        $tb = isset($b->tasktype) ? $b->tasktype->order : PHP_INT_MAX;
        if ($ta != $tb) {
            return $ta <=> $tb;
        }
        return $this->sorter_orderbycourse($a, $b);
    }

    /**
     * Loads activities with completion set in current course.
     *
     * @param boolean $selectedonly Whether to filter by configured selected items.
     * @return array
     */
    protected function load_activities($selectedonly) {
        $modinfo = get_fast_modinfo($this->course, -1);
        $sections = $modinfo->get_sections();
        $selectedonly = $selectedonly && ($this->blockconfig->activitiesincluded ?? '') === 'selectedactivities';
        $selectedcms = $this->blockconfig->selectactivities ?? [];

        $this->activities = [];

        foreach ($modinfo->instances as $module => $cms) {
            $modulename = get_string('pluginname', $module);
            foreach ($cms as $cm) {
                if ($cm->completion == COMPLETION_TRACKING_NONE) {
                    continue;
                }
                if ($selectedonly && !in_array($module . '-' . $cm->instance, $selectedcms)) {
                    continue;
                }

                $sectionkey = $cm->sectionnum;
                $positionkey = array_search($cm->id, $sections[$cm->sectionnum]);
                $sectinfo = $modinfo->get_section_info($cm->sectionnum);
                if (method_exists($sectinfo, 'is_delegated') && $sectinfo->is_delegated()) {
                    // If $cm lives within a section delegated to a module, use the parent cm's sectionnum
                    // as the section key, and make the position key be the parent cm's position followed
                    // by $cm's position in the delegated section.
                    $sectdelegate = $sectinfo->get_component_instance();
                    if ($sectdelegate instanceof \core_courseformat\sectiondelegatemodule) {
                        $parentcm = $sectdelegate->get_cm();
                        $sectionkey = $parentcm->sectionnum;
                        $positionkey = array_search($parentcm->id, $sections[$parentcm->sectionnum]) . ',' . $positionkey;
                    }
                }

                $this->activities[$cm->id] = (object)[
                    'type'       => $module,
                    'modulename' => $modulename,
                    'id'         => $cm->id,
                    'instance'   => $cm->instance,
                    'name'       => $cm->get_formatted_name(),
                    'expected'   => $cm->completionexpected,
                    'section'    => $sectionkey,
                    'position'   => $positionkey,
                    'url'        => $cm->url instanceof \moodle_url ? $cm->url->out() : '',
                    'onclick'    => $cm->onclick,
                    'context'    => $cm->context,
                    'icon'       => $cm->get_icon_url(),
                    'available'  => $cm->available,
                    'tasktype'   => task_types::match($cm->get_formatted_name()),
                ];
            }
        }
    }

    /**
     * Filter down the activities to those a user can see.
     *
     * Depending on the block setting 'showunavailable', activities that exist but are
     * (not yet) accessible for the user because of unmet access restrictions are kept
     * and marked as unavailable, so all students see bars of comparable length.
     */
    protected function filter_visible_activities() {
        if (!$this->user || $this->activities === null) {
            return;
        }

        $this->visibleactivities = [];
        $modinfo = get_fast_modinfo($this->course, $this->user->id);
        $greymode = $this->is_unavailable_grey_mode();
        $skipgroups = !empty($this->blockconfig->unavailableskipgroups ?? defaults::UNAVAILABLESKIPGROUPS);

        // Keep only activities that are visible.
        foreach ($this->activities as $key => $activity) {
            $cm = $modinfo->cms[$activity->id];
            $section = $cm->get_section_info();
            $activity->availableinfo = '';

            if (!$section->uservisible) {
                // Section hidden from the user. Keep it only if it is restricted (not hidden by the teacher).
                if (!$greymode || !$section->visible || $section->available || !$cm->visible) {
                    continue;
                }
                if ($skipgroups && $this->fails_group_restriction($section->availability,
                        new \core_availability\info_section($section))) {
                    continue;
                }
                if ($skipgroups && $this->fails_group_restriction($cm->availability,
                        new \core_availability\info_module($cm))) {
                    continue;
                }
                $activity->available = false;
                if (!empty($section->availableinfo)) {
                    $activity->availableinfo = \core_availability\info::format_info($section->availableinfo, $this->course);
                }
            } else if (!$cm->uservisible) {
                if (!$cm->visible) {
                    // Hidden by the teacher.
                    continue;
                }
                if (!$cm->availableinfo && !($greymode && !$cm->available)) {
                    // Restricted and hidden entirely, original behaviour.
                    continue;
                }
                if ($greymode && $skipgroups && $this->fails_group_restriction($cm->availability,
                        new \core_availability\info_module($cm))) {
                    continue;
                }
                $activity->available = false;
                if (!empty($cm->availableinfo)) {
                    $activity->availableinfo = \core_availability\info::format_info($cm->availableinfo, $this->course);
                }
            } else {
                $activity->available = true;
            }

            // Check for exclusions.
            if (in_array($activity->type . '-' . $activity->instance . '-' . $this->user->id, $this->exclusions)) {
                continue;
            }

            // Save the visible activity.
            $this->visibleactivities[$key] = $activity;
        }
    }

    /**
     * Split the cells into task type groups for the template.
     *
     * Activities are already sorted by task type (see sorter_orderbytasktype()),
     * so consecutive cells with the same type form one group.
     *
     * @param stdClass $data Template data, cells already built (same order as $activities)
     * @param array $activities
     * @param string $iconpos
     * @param bool $simple
     */
    protected function build_groups(stdClass $data, array $activities, string $iconpos, bool $simple): void {
        $groups = [];
        $current = null;
        foreach (array_values($activities) as $i => $activity) {
            $key = isset($activity->tasktype) ? $activity->tasktype->order : 'other';
            if ($current === null || $current->key !== $key) {
                $current = (object)[
                    'key' => $key,
                    'label' => isset($activity->tasktype) ? $activity->tasktype->label :
                        get_string('tasktype_other', 'block_completion_progress_tgm'),
                    'icon' => isset($activity->tasktype) ? $activity->tasktype->icon : '',
                    'cells' => [],
                ];
                $groups[] = $current;
            }
            $current->cells[] = $data->cells[$i];
        }

        $showheader = $iconpos !== self::TYPEICON_CELL;
        $cellsperrow = (int)($data->cellsperrow ?? 0);
        $data->grouped = true;
        $data->groups = [];
        foreach ($groups as $group) {
            $count = count($group->cells);
            if (!empty($data->barwrap) && $cellsperrow > 0) {
                $perrow = min($count, $cellsperrow);
                $groupstyle = 'flex: 0 0 calc(' . $perrow . ' * (100% - ' . ($cellsperrow - 1) . ' * var(--cpt-groupgap)) / ' .
                    $cellsperrow . ' + ' . ($perrow - 1) . ' * var(--cpt-groupgap));';
                $cellsstyle = 'flex-basis: calc(100% / ' . $perrow . ');';
            } else if (!empty($data->barscroll)) {
                $groupstyle = 'flex: 0 0 auto;';
                $cellsstyle = '';
            } else {
                $groupstyle = 'flex: ' . $count . ' 1 0;';
                $cellsstyle = '';
            }
            $data->groups[] = [
                'label' => $group->label,
                'icon' => $group->icon,
                'showheader' => $showheader,
                'groupstyle' => $groupstyle,
                'cellsstyle' => $cellsstyle,
                'cells' => $group->cells,
            ];
        }
    }

    /**
     * Whether unavailable activities are shown as grey cells.
     * @return bool
     */
    protected function is_unavailable_grey_mode(): bool {
        return ($this->blockconfig->showunavailable ?? defaults::SHOWUNAVAILABLE) === self::UNAVAILABLE_GREY;
    }

    /**
     * Check whether an availability tree contains a group or grouping condition
     * that the user does not meet and that is mandatory (joined by AND).
     *
     * Such activities are meant for other groups, so they are not counted as
     * outstanding work for this user.
     *
     * @param string|null $availability JSON availability structure
     * @param \core_availability\info $info
     * @return bool
     */
    protected function fails_group_restriction(?string $availability, \core_availability\info $info): bool {
        if (empty($availability)) {
            return false;
        }
        $structure = json_decode($availability);
        return $structure ? $this->fails_group_restriction_node($structure, $info) : false;
    }

    /**
     * Recursive helper for fails_group_restriction().
     * @param \stdClass $node
     * @param \core_availability\info $info
     * @return bool
     */
    private function fails_group_restriction_node(\stdClass $node, \core_availability\info $info): bool {
        if (($node->op ?? '') !== '&' || empty($node->c) || !is_array($node->c)) {
            return false;
        }
        foreach ($node->c as $child) {
            if (isset($child->c)) {
                if ($this->fails_group_restriction_node($child, $info)) {
                    return true;
                }
                continue;
            }
            if (!isset($child->type) || !in_array($child->type, ['group', 'grouping'])) {
                continue;
            }
            $classname = '\\availability_' . $child->type . '\\condition';
            if (!class_exists($classname)) {
                continue;
            }
            try {
                $condition = new $classname($child);
            } catch (\Throwable $e) {
                continue;
            }
            if (!$condition->is_available(false, $info, true, $this->user->id)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Finds gradebook exclusions for students in the course.
     */
    protected function load_exclusions() {
        global $DB;

        if ($this->exclusionsforall) {
            // Already loaded.
            return;
        }

        $query = "SELECT g.id, i.itemmodule, i.iteminstance, g.userid
                   FROM {grade_grades} g, {grade_items} i
                  WHERE i.courseid = :courseid
                    AND i.id = g.itemid
                    AND g.excluded <> 0";
        $params = ['courseid' => $this->course->id];
        if ($this->user) {
            $query .= " AND g.userid = :userid";
            $params['userid'] = $this->user->id;
        } else {
            // Avoid refetching this info if specialising for user later.
            $this->exclusionsforall = true;
        }

        $this->exclusions = [];
        foreach ($DB->get_records_sql($query, $params) as $rec) {
            $this->exclusions[] = $rec->itemmodule . '-' . $rec->iteminstance . '-' . $rec->userid;
        }
    }

    /**
     * Loads completion information for enrolled users in the course.
     */
    protected function load_completions() {
        global $DB;

        if ($this->completionsforall) {
            // Already loaded.
            return;
        }

        // Somewhat faster than lots of calls to completion_info::get_data($cm, true, $userid)
        // where its cache can't be used because the userid is different.
        $enrolsql = get_enrolled_join($this->context, 'u.id', false);
        $query = "SELECT DISTINCT " . $DB->sql_concat('cm.id', "'-'", 'u.id') . " AS id,
                        u.id AS userid, cm.id AS cmid,
                        COALESCE(cmc.completionstate, :incomplete) AS completionstate
                    FROM {user} u {$enrolsql->joins}
              CROSS JOIN {course_modules} cm
               LEFT JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id AND cmc.userid = u.id
                   WHERE {$enrolsql->wheres}
                     AND cm.course = :courseid
                     AND cm.completion <> :none";
        $params = $enrolsql->params + [
            'courseid' => $this->course->id,
            'incomplete' => COMPLETION_INCOMPLETE,
            'none' => COMPLETION_TRACKING_NONE,
        ];
        if ($this->user) {
            $query .= " AND u.id = :userid";
            $params['userid'] = $this->user->id;
        } else {
            // Avoid refetching this info if specialising for user later.
            $this->completionsforall = true;
        }

        $rset = $DB->get_recordset_sql($query, $params);
        $this->completions = [];
        foreach ($rset as $compl) {
            $submission = $this->submissions[$compl->userid][$compl->cmid] ?? null;

            if ($compl->completionstate == COMPLETION_INCOMPLETE && $submission) {
                $this->completions[$compl->userid][$compl->cmid] = 'submitted';
            } else if (
                $compl->completionstate == COMPLETION_COMPLETE_FAIL &&
                $submission &&
                !$submission->graded
            ) {
                $this->completions[$compl->userid][$compl->cmid] = 'submitted';
            } else {
                $this->completions[$compl->userid][$compl->cmid] = $compl->completionstate;
            }
        }
        $rset->close();
    }

    /**
     * Find submissions for students in the course.
     */
    protected function load_submissions() {
        global $DB, $CFG;

        if ($this->submissionsforall) {
            // Already loaded.
            return;
        }

        require_once($CFG->dirroot . '/mod/quiz/lib.php');

        $params = [
            'courseid' => $this->course->id,
        ];

        if ($this->user) {
            $assignwhere = 'AND s.userid = :userid';
            $workshopwhere = 'AND s.authorid = :userid';
            $quizwhere = 'AND qa.userid = :userid';

            $params += [
              'userid' => $this->user->id,
            ];
        } else {
            $assignwhere = '';
            $workshopwhere = '';
            $quizwhere = '';

            // Avoid refetching this info if specialising for user later.
            $this->submissionsforall = true;
        }

        // Queries to deliver instance IDs of activities with submissions by user.
        $queries = [
            [
                // Assignments with individual submission, or groups requiring a submission per user,
                // or ungrouped users in a group submission situation.
                'module' => 'assign',
                'query' => "SELECT {$DB->sql_concat('s.userid', "'-'", 'c.id')} AS id,
                             s.userid, c.id AS cmid,
                             MAX(CASE WHEN ag.grade IS NULL OR ag.grade = -1 THEN 0 ELSE 1 END) AS graded
                          FROM {assign_submission} s
                            INNER JOIN {assign} a ON s.assignment = a.id
                            INNER JOIN {course_modules} c ON c.instance = a.id
                            INNER JOIN {modules} m ON m.name = 'assign' AND m.id = c.module
                            LEFT JOIN {assign_grades} ag ON ag.assignment = s.assignment
                                  AND ag.attemptnumber = s.attemptnumber
                                  AND ag.userid = s.userid
                          WHERE s.latest = 1
                            AND s.status = 'submitted'
                            AND a.course = :courseid
                            AND (
                                a.teamsubmission = 0 OR
                                (a.teamsubmission <> 0 AND a.requireallteammemberssubmit <> 0 AND s.groupid = 0) OR
                                (a.teamsubmission <> 0 AND a.preventsubmissionnotingroup = 0 AND s.groupid = 0)
                            )
                            $assignwhere
                        GROUP BY s.userid, c.id",
                'params' => [ ],
            ],

            [
                // Assignments with groups requiring only one submission per group.
                'module' => 'assign',
                'query' => "SELECT {$DB->sql_concat('s.userid', "'-'", 'c.id')} AS id,
                             s.userid, c.id AS cmid,
                             MAX(CASE WHEN ag.grade IS NULL OR ag.grade = -1 THEN 0 ELSE 1 END) AS graded
                          FROM {assign_submission} gs
                            INNER JOIN {assign} a ON gs.assignment = a.id
                            INNER JOIN {course_modules} c ON c.instance = a.id
                            INNER JOIN {modules} m ON m.name = 'assign' AND m.id = c.module
                            INNER JOIN {groups_members} s ON s.groupid = gs.groupid
                            LEFT JOIN {assign_grades} ag ON ag.assignment = gs.assignment
                                  AND ag.attemptnumber = gs.attemptnumber
                                  AND ag.userid = s.userid
                          WHERE gs.latest = 1
                            AND gs.status = 'submitted'
                            AND gs.userid = 0
                            AND a.course = :courseid
                            AND (a.teamsubmission <> 0 AND a.requireallteammemberssubmit = 0)
                            $assignwhere
                        GROUP BY s.userid, c.id",
                'params' => [ ],
            ],

            [
                'module' => 'workshop',
                'query' => "SELECT {$DB->sql_concat('s.authorid', "'-'", 'c.id')} AS id,
                               s.authorid AS userid, c.id AS cmid,
                               1 AS graded
                             FROM {workshop_submissions} s, {workshop} w, {modules} m, {course_modules} c
                            WHERE s.workshopid = w.id
                              AND w.course = :courseid
                              AND m.name = 'workshop'
                              AND m.id = c.module
                              AND c.instance = w.id
                              $workshopwhere
                          GROUP BY s.authorid, c.id",
                'params' => [ ],
            ],

            [
                // Quizzes with 'first' and 'last attempt' grading methods.
                'module' => 'quiz',
                'query' => "SELECT {$DB->sql_concat('qa.userid', "'-'", 'c.id')} AS id,
                           qa.userid, c.id AS cmid,
                           (CASE WHEN qa.sumgrades IS NULL THEN 0 ELSE 1 END) AS graded
                         FROM {quiz_attempts} qa
                           INNER JOIN {quiz} q ON q.id = qa.quiz
                           INNER JOIN {course_modules} c ON c.instance = q.id
                           INNER JOIN {modules} m ON m.name = 'quiz' AND m.id = c.module
                        WHERE qa.state = 'finished'
                          AND q.course = :courseid
                          AND qa.attempt = (
                            SELECT CASE WHEN q.grademethod = :gmfirst THEN MIN(qa1.attempt)
                                        WHEN q.grademethod = :gmlast THEN MAX(qa1.attempt) END
                            FROM {quiz_attempts} qa1
                            WHERE qa1.quiz = qa.quiz
                              AND qa1.userid = qa.userid
                              AND qa1.state = 'finished'
                          )
                          $quizwhere",
                'params' => [
                    'gmfirst' => QUIZ_ATTEMPTFIRST,
                    'gmlast' => QUIZ_ATTEMPTLAST,
                ],
            ],
            [
                // Quizzes with 'maximum' and 'average' grading methods.
                'module' => 'quiz',
                'query' => "SELECT {$DB->sql_concat('qa.userid', "'-'", 'c.id')} AS id,
                           qa.userid, c.id AS cmid,
                           MIN(CASE WHEN qa.sumgrades IS NULL THEN 0 ELSE 1 END) AS graded
                         FROM {quiz_attempts} qa
                           INNER JOIN {quiz} q ON q.id = qa.quiz
                           INNER JOIN {course_modules} c ON c.instance = q.id
                           INNER JOIN {modules} m ON m.name = 'quiz' AND m.id = c.module
                        WHERE (q.grademethod = :gmmax OR q.grademethod = :gmavg)
                          AND qa.state = 'finished'
                          AND q.course = :courseid
                          $quizwhere
                       GROUP BY qa.userid, c.id",
                'params' => [
                    'gmmax' => QUIZ_GRADEHIGHEST,
                    'gmavg' => QUIZ_GRADEAVERAGE,
                ],
            ],
        ];

        $this->submissions = [];
        foreach ($queries as $spec) {
            $results = $DB->get_records_sql($spec['query'], $params + $spec['params']);
            foreach ($results as $obj) {
                unset($obj->id);
                $this->submissions[$obj->userid][$obj->cmid] = $obj;
            }
        }
    }

    /**
     * Produce template data for rendering.
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(\renderer_base $output): stdClass {
        global $CFG, $USER;

        $data = new stdClass();

        $clock = \core\di::get(\core\clock::class);
        $now = $clock->time();
        $activities = $this->get_visible_activities();
        $completions = $this->get_completions();
        $config = $this->get_block_config();
        $userid = $this->get_user()->id;
        $courseid = $this->get_course()->id;
        $instance = $this->get_block_instance()->id;
        $simple = $this->is_simple_bar();
        $numactivities = count($activities);
        $greymode = $this->is_unavailable_grey_mode();
        $showtypes = !empty($config->showtasktypes ?? defaults::SHOWTASKTYPES);
        $showlegend = $showtypes && !$simple && !empty($config->showtasktypelegend ?? defaults::SHOWTASKTYPELEGEND);
        $legend = [];
        $grouped = (($config->orderby ?? defaults::ORDERBY) === self::ORDERBY_TASKTYPE);
        $iconpos = $grouped ? ($config->tasktypeiconpos ?? defaults::TASKTYPEICONPOS) : self::TYPEICON_CELL;
        $iconsincells = $iconpos !== self::TYPEICON_HEADER;

        // Get relevant block instance settings or use defaults.
        $useicons = get_config('block_completion_progress_tgm', 'forceiconsinbar') ?:
            ($config->progressBarIcons ?? defaults::PROGRESSBARICONS);
        $orderby = $config->orderby ?? defaults::ORDERBY;
        $longbars = $config->longbars ??
            (get_config('block_completion_progress_tgm', 'defaultlongbars') ?: defaults::LONGBARS);
        $displaynow = $orderby == self::ORDERBY_TIME;
        $showpercentage = $config->showpercentage ?? defaults::SHOWPERCENTAGE;

        $alternatelinks = [
            'assign' => [
                'url' => '/mod/assign/view.php?id=:cmid&action=grade&userid=:userid',
                'capability' => 'mod/assign:grade',
            ],
            'feedback' => [
                // Breaks if anonymous feedback is collected.
                'url' => '/mod/feedback/show_entries.php?id=:cmid&do_show=showoneentry&userid=:userid',
                'capability' => 'mod/feedback:viewreports',
            ],
            'lesson' => [
                'url' => '/mod/lesson/report.php?id=:cmid&action=reportdetail&userid=:userid',
                'capability' => 'mod/lesson:viewreports',
            ],
            'quiz' => [
                'url' => '/mod/quiz/report.php?id=:cmid&mode=overview',
                'capability' => 'mod/quiz:viewreports',
            ],
        ];

        $data->courseid = $courseid;
        $data->instanceid = $instance;
        $data->userid = $userid;
        $data->simple = $simple;
        $data->useicons = $useicons;

        if ($simple && $numactivities == 0) {
            $data->novisibleactivities = true;
            return $data;
        }

        $wrapafter = max(1, get_config('block_completion_progress_tgm', 'wrapafter') ?: defaults::WRAPAFTER);
        if ($longbars == 'wrap' && $numactivities > $wrapafter) {
            $data->barwrap = true;
            $data->cellsperrow = ceil($numactivities / max(1, ceil($numactivities / $wrapafter)));
            $displaynow = false;
        } else if ($longbars == 'scroll') {
            $data->barscroll = true;
        } else {
            $longbars = 'squeeze';
            $data->barsqueeze = true;
        }

        // Determine where to put the NOW indicator.
        $nowpos = -1;
        if ($orderby == 'orderbytime' && $longbars != 'wrap' && $displaynow && !$simple) {
            $data->displaynow = true;

            $nowpos = 0;
            while ($nowpos < $numactivities && $now > $activities[$nowpos]->expected && $activities[$nowpos]->expected != 0) {
                $nowpos++;
            }
        }

        if ($showpercentage && !$simple) {
            $data->progresspercentage = $this->get_percentage() . '%';
        }

        // Determine links to activities.
        for ($i = 0; $i < $numactivities; $i++) {
            if (
                $userid != $USER->id &&
                array_key_exists($activities[$i]->type, $alternatelinks) &&
                has_capability($alternatelinks[$activities[$i]->type]['capability'], $activities[$i]->context)
            ) {
                $substitutions = [
                    '/:courseid/' => $courseid,
                    '/:eventid/'  => $activities[$i]->instance,
                    '/:cmid/'     => $activities[$i]->id,
                    '/:userid/'   => $userid,
                ];
                $link = $alternatelinks[$activities[$i]->type]['url'];
                $link = preg_replace(array_keys($substitutions), array_values($substitutions), $link);
                $activities[$i]->link = $CFG->wwwroot . $link;
            } else {
                $activities[$i]->link = $activities[$i]->url;
            }
        }

        $data->cells = [];

        // Determine the bar cells and information blocks.
        $counter = 1;
        foreach ($activities as $activity) {
            $complete = $completions[$activity->id] ?? null;

            // A cell in the progress bar.
            $cell = new stdClass();
            $cell->activityid = $activity->id;

            if ($complete === 'submitted') {
                $cell->submittednotcomplete = true;
            } else if ($complete == COMPLETION_COMPLETE || $complete == COMPLETION_COMPLETE_PASS) {
                $cell->completed = true;
            } else if ($complete == COMPLETION_COMPLETE_FAIL) {
                $cell->notcompleted = true;
            } else if ($greymode && empty($activity->available)) {
                $cell->unavailable = true;
            } else if (
                (!isset($config->orderby) || $config->orderby == 'orderbytime') &&
                (isset($activity->expected) && $activity->expected > 0 && $activity->expected < $now)
            ) {
                $cell->notcompleted = true;
            } else {
                $cell->futurenotcompleted = true;
            }
            if (empty($activity->link)) {
                $cell->haslink = 'false';
            } else if (!empty($activity->available) || $simple) {
                $cell->haslink = 'true';
            } else if (!empty($activity->link)) {
                $cell->haslink = 'not-allowed';
            }

            // Place the NOW indicator.
            if ($nowpos == 0 && $counter == 1) {
                $cell->firstnow = true;
            } else if ($nowpos == $counter) {
                if ($nowpos < $numactivities / 2) {
                    $cell->firsthalfnow = true;
                } else {
                    $cell->lasthalfnow = true;
                }
            }

            $cell->activityicon = $activity->icon->out(false);
            $cell->activityname = $activity->name;
            if (!empty($activity->link) && (!empty($activity->available) || $simple)) {
                $cell->activitylink = $activity->link;
                if (!empty($activity->onclick)) {
                    $cell->activityonclick = $activity->onclick;
                }
            }
            if ($complete == COMPLETION_COMPLETE) {
                $cell->infocomplete = true;
                $cell->infoicon = 'tick';
            } else if ($complete == COMPLETION_COMPLETE_PASS) {
                $cell->infopassed = true;
                $cell->infoicon = 'tick';
            } else if ($complete == COMPLETION_COMPLETE_FAIL) {
                $cell->infofailed = true;
                $cell->infoicon = 'cross';
            } else if ($complete === 'submitted') {
                $cell->infosubmitted = true;
                $cell->infoicon = 'cross';
            } else {
                $cell->infoincomplete = true;
                $cell->infoicon = 'cross';
            }
            if ($showtypes && !empty($activity->tasktype)) {
                $cell->typelabel = $activity->tasktype->label;
                if ($iconsincells) {
                    $cell->hastype = true;
                    $cell->typeicon = $activity->tasktype->icon;
                }
                $legend[$activity->tasktype->order] = $activity->tasktype;
            }
            if ($greymode && empty($activity->available)) {
                $cell->infounavailable = true;
                if (!empty($activity->availableinfo)) {
                    $cell->availabilityinfo = $activity->availableinfo;
                }
            }
            if ($activity->expected != 0) {
                $cell->activityexpected = $activity->expected;
            }

            $data->cells[] = $cell;

            $counter++;
        }

        if ($grouped) {
            $this->build_groups($data, $activities, $iconpos, $simple);
        }

        if ($showlegend && $legend) {
            ksort($legend);
            $data->haslegend = true;
            $data->legend = array_map(fn($t) => ['icon' => $t->icon, 'label' => $t->label], array_values($legend));
        }

        return $data;
    }
}
