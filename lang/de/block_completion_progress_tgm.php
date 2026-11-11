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
 * German strings for the local extensions (all other strings come from the AMOS language pack).
 *
 * @package    block_completion_progress_tgm
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['config_default_title'] = 'Fortschrittsbalken (TGM)';
$string['config_showunavailable'] = '(Noch) nicht erreichbare Aktivitäten';
$string['config_showunavailable_help'] = '<p>Aktivitäten können für Teilnehmer/innen (noch) nicht erreichbar sein, z.B. weil eine Voraussetzung (Aktivitätsabschluss, Datum, Bewertung, ...) nicht erfüllt ist.</p><ul><li><b>Als graue Felder anzeigen</b>: Jede solche Aktivität erscheint als graues Feld, auch wenn sie durch die Voraussetzung auf der Kursseite vollständig verborgen ist. Alle sehen einen gleich langen Balken, der Prozentwert bezieht sich auf alle Aktivitäten.</li><li><b>Ursprüngliches Verhalten</b>: Vollständig verborgene Aktivitäten werden weggelassen, ausgegraute in den normalen Farben angezeigt.</li></ul><p>Von Lehrenden verborgene Aktivitäten werden nie angezeigt.</p>';
$string['config_unavailable_grey'] = 'Als graue Felder anzeigen';
$string['config_unavailable_hide'] = 'Ursprüngliches Verhalten (verborgene weglassen)';
$string['config_unavailableskipgroups'] = 'Aktivitäten für andere Gruppen weglassen';
$string['config_unavailableskipgroups_help'] = '<p>Wenn aktiviert, wird eine nicht erreichbare Aktivität weggelassen, wenn sie (über eine verpflichtende UND-Bedingung) auf eine Gruppe oder Gruppierung eingeschränkt ist, der die Person nicht angehört. Solche Aktivitäten sind für andere bestimmt und gehören nicht zum Arbeitsumfang.</p>';
$string['pluginname'] = 'Fortschrittsbalken (TGM)';
$string['unavailable'] = 'Noch nicht verfügbar';
$string['unavailable_colour'] = '#B0B0B0';
$string['unavailable_colour_descr'] = 'HTML-Farbcode für Elemente, die (noch) nicht erreichbar sind';
$string['unavailable_colour_title'] = 'Farbe für nicht verfügbar';
