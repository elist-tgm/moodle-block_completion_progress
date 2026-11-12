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

namespace block_completion_progress_tgm;

/**
 * Task types (function of an activity in the learning process), detected by a keyword
 * at the beginning of the activity name.
 *
 * Definitions come from the admin setting 'tasktypes', one per line:
 *     Keyword | icon classes | label (optional)
 * e.g. "Üben | fa-dumbbell | Üben".
 *
 * @package    block_completion_progress_tgm
 * @copyright  2026 Erhard List <elist@tgm.ac.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class task_types {
    /** @var array|null Parsed definitions, cached per request. */
    private static $definitions = null;

    /**
     * Parse a definition text.
     * @param string $text
     * @return array of objects with keyword, icon, label, order
     */
    public static function parse(string $text): array {
        $result = [];
        $order = 0;
        foreach (preg_split('/\R/u', $text) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $parts = array_map('trim', explode('|', $line));
            $keyword = $parts[0];
            if ($keyword === '') {
                continue;
            }
            $icon = self::clean_icon($parts[1] ?? '');
            $label = ($parts[2] ?? '') !== '' ? $parts[2] : $keyword;
            $result[] = (object)[
                'keyword' => $keyword,
                'icon' => $icon,
                'label' => $label,
                'order' => $order++,
            ];
        }
        // Longest keyword first, so that "Übenplus" would win over "Üben" if both exist.
        usort($result, fn($a, $b) => \core_text::strlen($b->keyword) <=> \core_text::strlen($a->keyword));
        return $result;
    }

    /**
     * Normalise icon classes: only safe characters, add the Font Awesome base class if missing.
     * @param string $icon e.g. "fa-dumbbell" or "fa-solid fa-dumbbell"
     * @return string
     */
    public static function clean_icon(string $icon): string {
        $icon = trim(preg_replace('/[^a-z0-9 _-]/i', '', $icon));
        if ($icon === '') {
            return '';
        }
        if (!preg_match('/(^|\s)(fa|fa-solid|fa-regular|fa-brands|fas|far|fab)(\s|$)/', $icon)) {
            $icon = 'fa ' . $icon;
        }
        return $icon;
    }

    /**
     * Return the configured definitions.
     * @return array
     */
    public static function get_definitions(): array {
        if (self::$definitions === null) {
            $text = get_config('block_completion_progress_tgm', 'tasktypes');
            if ($text === false || $text === null) {
                $text = defaults::TASKTYPES;
            }
            self::$definitions = self::parse((string)$text);
        }
        return self::$definitions;
    }

    /**
     * Reset the cache (settings change, unit tests).
     */
    public static function reset_cache(): void {
        self::$definitions = null;
    }

    /**
     * Find the task type of an activity name.
     *
     * The keyword must be the first word of the name: it may be followed by a space,
     * punctuation (":", "-", ...) or the end of the name, but not by a letter or digit.
     * Matching is case-insensitive.
     *
     * @param string $name Formatted activity name (may contain HTML/entities)
     * @param array|null $definitions Defaults to the configured definitions
     * @return \stdClass|null
     */
    public static function match(string $name, ?array $definitions = null): ?\stdClass {
        $definitions = $definitions ?? self::get_definitions();
        if (empty($definitions)) {
            return null;
        }
        $plain = trim(html_entity_decode(strip_tags($name), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        foreach ($definitions as $def) {
            $pattern = '/^' . preg_quote($def->keyword, '/') . '(?![\p{L}\p{N}_])/iu';
            if (preg_match($pattern, $plain)) {
                return $def;
            }
        }
        return null;
    }
}
