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
 * German strings for block_completion_progress_tgm.
 *
 * Based on the AMOS translation of block_completion_progress (de, 5.1),
 * extended by the strings of the TGM extensions.
 *
 * @package    block_completion_progress_tgm
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['completed_colour'] = '#6A9C35';
$string['completed_colour_descr'] = 'HTML-Farbcode für Elemente, die abgeschlossen sind';
$string['completed_colour_title'] = 'Abschlussfarbe';
$string['completion_not_enabled'] = 'Abschlussverfolgung ist für diese Website nicht aktiviert.';
$string['completion_not_enabled_course'] = 'Abschlussverfolgung ist in diesem Kurs nicht aktiviert.';
$string['completion_progress_tgm:addinstance'] = 'Neuen Block \'Fortschrittsbalken (TGM)\' hinzufügen';
$string['completion_progress_tgm:myaddinstance'] = 'Neuen Block \'Fortschrittsbalken (TGM)\' zum Dashboard hinzufügen';
$string['completion_progress_tgm:overview'] = 'Übersicht zum Bearbeitungsfortschritt für alle Kursteilnehmer/innen anzeigen';
$string['completion_progress_tgm:showbar'] = 'Balken im Block \'Fortschrittsbalken (TGM)\' anzeigen';
$string['computeprogress'] = 'Fortschritt in Prozent berechnen';
$string['config_activitiesincluded'] = 'Aktivitäten enthalten';
$string['config_activitycompletion'] = 'Alle Aktivitäten mit aktivierter Abschlussverfolgung';
$string['config_default_title'] = 'Fortschrittsbalken (TGM)';
$string['config_group'] = 'Nur für Gruppe oder Gruppierung sichtbar';
$string['config_header_monitored'] = 'Überwacht';
$string['config_icons'] = 'Icons im Balken nutzen';
$string['config_longbars'] = 'Anzeige langer Balken';
$string['config_orderby'] = 'Balken anordnen nach';
$string['config_orderby_course_order'] = 'Anordnung im Kurs';
$string['config_orderby_due_time'] = 'Zeit mit \'{$a}\' Datum';
$string['config_percentage'] = 'Prozente für Teilnehmer/innen anzeigen';
$string['config_scroll'] = 'Scrollen';
$string['config_selectactivities'] = 'Aktivitäten auswählen';
$string['config_selectedactivities'] = 'Ausgewählte Aktivitäten';
$string['config_showunavailable'] = '(Noch) nicht erreichbare Aktivitäten';
$string['config_showunavailable_help'] = '<p>Aktivitäten können für Teilnehmer/innen (noch) nicht erreichbar sein, z.B. weil eine Voraussetzung (Aktivitätsabschluss, Datum, Bewertung, ...) nicht erfüllt ist.</p><ul><li><b>Als graue Felder anzeigen</b>: Jede solche Aktivität erscheint als graues Feld, auch wenn sie durch die Voraussetzung auf der Kursseite vollständig verborgen ist. Alle sehen einen gleich langen Balken, der Prozentwert bezieht sich auf alle Aktivitäten.</li><li><b>Ursprüngliches Verhalten</b>: Vollständig verborgene Aktivitäten werden weggelassen, ausgegraute in den normalen Farben angezeigt.</li></ul><p>Von Lehrenden verborgene Aktivitäten werden nie angezeigt.</p>';
$string['config_squeeze'] = 'Skaliert';
$string['config_title'] = 'Alternativer Titel';
$string['config_unavailable_grey'] = 'Als graue Felder anzeigen';
$string['config_unavailable_hide'] = 'Ursprüngliches Verhalten (verborgene weglassen)';
$string['config_unavailableskipgroups'] = 'Aktivitäten für andere Gruppen weglassen';
$string['config_unavailableskipgroups_help'] = '<p>Wenn aktiviert, wird eine nicht erreichbare Aktivität weggelassen, wenn sie (über eine verpflichtende UND-Bedingung) auf eine Gruppe oder Gruppierung eingeschränkt ist, der die Person nicht angehört. Solche Aktivitäten sind für andere bestimmt und gehören nicht zum Arbeitsumfang.</p>';
$string['config_wrap'] = 'Umbruch';
$string['coursenametoshow'] = 'Kursname im Dashboard';
$string['defaultlongbars'] = 'Standardmäßige Darstellung für lange Balken';
$string['errorinvalidblockinstanceid'] = 'Ungültige Block-Instanz-ID angegeben.';
$string['forceiconsinbar'] = 'Icons im Fortschrittsbalken erzwingen';
$string['fullname'] = 'Vollständiger Kursname';
$string['futureNotCompleted_colour'] = '#025187';
$string['futureNotCompleted_colour_descr'] = 'HTML-Farbcode für demnächst fällige Elemente, die noch nicht abgeschlossen sind';
$string['futureNotCompleted_colour_title'] = 'Farbe für zukünftig und nicht abgeschlossen';
$string['how_activitiesincluded_works'] = 'Wie Einfügen von Aktivitäten funktioniert';
$string['how_activitiesincluded_works_help'] = '<p>Standardmäßig werden alle Aktivitäten mit Aktivitätsabschluss in den Balken angezeigt.</p><p>Sie können die einzubeziehenden Aktivitäten auch manuell auswählen.</p>';
$string['how_group_works'] = 'Wie sichtbare Gruppen funktionieren';
$string['how_group_works_help'] = '<p>Wenn Sie eine Gruppe oder Gruppierung auswählen, wird die Anzeige dieses Blocks auf diese Gruppe oder Gruppierung beschränkt.</p>';
$string['how_longbars_works'] = 'Anzeige langer Balken';
$string['how_longbars_works_help'] = '<p>Wenn Balken eine bestimmte Länge überschreiten, können sie auf eine der folgenden Arten dargestellt werden:</p><ul><li>Auf die verfügbare Breite skaliert</li><li>Seitliches Scrollen, um überlaufende Balkensegmente anzuzeigen</li><li>Zeilenumbruch innerhalb des Balkens</li></ul><p>Beachten Sie, dass bei Zeilenumbruch die JETZT-Markierung nicht angezeigt wird.</p>';
$string['how_ordering_works'] = 'Wie das Ordnen funktioniert';
$string['how_ordering_works_help'] = '<p>Es gibt zwei Möglichkeiten, Aktivitäten im Block \'Fortschrittsbalken\' anzuordnen.</p><ul><li><em>Datum der erwarteten Fertigstellung</em> (Standard)<br />Die erwarteten Fertigstellungstermine der Aktivitäten/Ressourcen werden für die Anordnung verwendet. Hat eine Aktivität/Ressource kein erwartetes Fertigstellungsdatum, wird stattdessen die Reihenfolge im Kurs verwendet. Bei dieser Option wird die JETZT-Markierung angezeigt.</li><li><em>Anordnung im Kurs</em><br />Aktivitäten/Ressourcen werden in derselben Reihenfolge wie auf der Kursseite dargestellt. Erwartete Fertigstellungstermine werden dabei ignoriert und die JETZT-Markierung wird nicht angezeigt.</li></ul>';
$string['how_selectactivities_works'] = 'Wie das Einbeziehen von Aktivitäten funktioniert';
$string['how_selectactivities_works_help'] = '<p>Um Aktivitäten manuell in die Leiste aufzunehmen, müssen Sie die Option "Aktivitäten enthalten" auf "Ausgewählte Aktivitäten" setzen.</p><p>Nur Aktivitäten mit aktivierter Abschlussverfolgung können aufgenommen werden.</p><p>Halten Sie die STRG-Taste gedrückt, um mehrere Aktivitäten auszuwählen.</p>';
$string['indeterminate'] = '?';
$string['lastonline'] = 'Zuletzt im Kurs';
$string['mouse_over_prompt'] = 'Um mehr Informationen anzuzeigen, mit der Maus über den Balken fahren oder einzelne Blöcke anklicken';
$string['no_activities_config_message'] = 'Es gibt keine Aktivitäten oder Ressourcen mit Aktivitätsabschluss oder keine Aktivitäten oder Ressourcen wurden ausgewählt. Aktivieren Sie den Aktivitätsabschluss für Aktivitäten und Ressourcen und konfigurieren Sie dann diesen Block.';
$string['no_activities_message'] = 'Es werden keine Aktivitäten oder Ressourcen beobachtet. Stellen Sie dies in den Einstellungen ein.';
$string['no_blocks'] = 'Es ist kein Block \'Fortschrittsbalken\' für Ihre Kurse aktiviert.';
$string['no_courses'] = 'Sie sind in keine Kurse eingeschrieben. Es wird nur der Bearbeitungsfortschritt von Kursen angezeigt, in die Sie eingeschrieben sind.';
$string['no_visible_activities_message'] = 'Keine der überwachten Aktivitäten ist derzeit sichtbar.';
$string['notCompleted_colour'] = '#C71C22';
$string['notCompleted_colour_descr'] = 'HTML-Farbcode für die aktuelle Elemente, die noch nicht abgeschlossen sind.';
$string['notCompleted_colour_title'] = 'Farbe für nicht-abgeschlossen';
$string['not_all_expected_set'] = 'Nicht alle Aktivitäten mit aktivierter Abschlussverfolgung haben ein \'{$a}\'-Datum gesetzt.';
$string['now_indicator'] = 'JETZT';
$string['overview'] = 'Überblick Teilnehmer/innen';
$string['overviewcachetime'] = 'Zeit, um berechneten Fortschritt in Prozent zu cachen';
$string['pluginname'] = 'Fortschrittsbalken (TGM)';
$string['privacy:metadata:block_completion_progress_tgm:blockinstanceid'] = 'Die ID des Fortschrittsbalken-Blocks, auf den sich der Prozentsatz bezieht.';
$string['privacy:metadata:block_completion_progress_tgm:courseid'] = 'Die ID des Kurses, auf den sich der Prozentsatz bezieht.';
$string['privacy:metadata:block_completion_progress_tgm:percentage'] = 'Der Abschluss in Prozent';
$string['privacy:metadata:block_completion_progress_tgm:tableexplanation'] = 'Der Block Fortschrittsbalken speichert den Abschluss in Prozent zwischen, um die Übersichtstabelle sortieren zu können.';
$string['privacy:metadata:block_completion_progress_tgm:timemodified'] = 'Der Zeitpunkt der Berechnung des Prozentsatzes.';
$string['privacy:metadata:block_completion_progress_tgm:userid'] = 'Die Nutzer-ID des zugehörigen Prozentsatzes.';
$string['progress'] = 'Fortschritt';
$string['progressbar'] = 'Fortschrittsbalken (TGM)';
$string['progresscachetime'] = 'Vor {$a} berechnet';
$string['selectitem'] = '{$a} auswählen';
$string['shortname'] = 'Kurzer Kursname';
$string['showallinfo'] = 'Alle Infos anzeigen';
$string['showinactive'] = 'Inaktive Teilnehmer/innen in der Übersicht anzeigen';
$string['showlastincourse'] = 'In der Übersicht anzeigen, wann Teilnehmer/innen zuletzt im Kurs waren';
$string['submitted'] = 'Eingereicht';
$string['submittednotcomplete_colour'] = '#FFCC00';
$string['submittednotcomplete_colour_descr'] = 'HTML-Farbcode für Elemente, die eingereicht, aber noch nicht abgeschlossen sind.';
$string['submittednotcomplete_colour_title'] = 'Farbe für eingereicht und noch nicht abgeschlossen';
$string['time_expected'] = 'Erwartet';
$string['unavailable'] = 'Noch nicht verfügbar';
$string['unavailable_colour'] = '#B0B0B0';
$string['unavailable_colour_descr'] = 'HTML-Farbcode für Elemente, die (noch) nicht erreichbar sind';
$string['unavailable_colour_title'] = 'Farbe für nicht verfügbar';
$string['why_set_the_title'] = 'Warum Sie den Block Beispiel-Titel setzten sollten.';
$string['why_set_the_title_help'] = '<p>Es können mehrere Instanzen des Blocks \'Fortschrittsbalken\' vorhanden sein. So können Sie verschiedene Blöcke verwenden, um unterschiedliche Gruppen von Aktivitäten oder Ressourcen zu überwachen, z.B. Aufgaben in einem Block und Tests in einem anderen. Deshalb können Sie den Standardtitel für jede Instanz durch einen passenderen Titel ersetzen.</p>';
$string['why_show_precentage'] = 'Warum Sie den Bearbeitungsfortschritt in Prozenten anzeigen sollten?';
$string['why_show_precentage_help'] = '<p>Es ist möglich, den Teilnehmer/innen einen Gesamtprozentsatz des Fortschritts anzuzeigen.</p><p>Dieser wird berechnet als Anzahl der abgeschlossenen Aktivitäten geteilt durch die Gesamtzahl der Aktivitäten im Balken.</p><p>Der Prozentwert wird angezeigt, bis die Maus über ein Element im Balken bewegt wird.</p>';
$string['why_use_icons'] = 'Warum Sie Icons nutzen sollten?';
$string['why_use_icons_help'] = '<p>Vielleicht möchten Sie Häkchen- und Kreuz-Icons für den Fortschrittsbalken hinzufügen, um diesen Block für Teilnehmer/innen mit Farbsehschwäche besser zugänglich zu machen.</p><p>Icons können die Bedeutung des Blocks auch verdeutlichen, wenn Farben aus kulturellen oder persönlichen Gründen nicht intuitiv sind.</p>';
$string['wrapafter'] = 'Beim Umbruch auf Zeilen beschränken mit';
