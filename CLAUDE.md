# CLAUDE.md – block_completion_progress_tgm

Hinweise für Claude (und andere KI-Assistenten), die in diesem Repository arbeiten.
Sprache im Gespräch: Deutsch. Code, Kommentare, Commit-Messages und englische Sprachdatei: Englisch.

## Projekt

- Fork von [jonof/moodle-block_completion_progress](https://github.com/jonof/moodle-block_completion_progress)
  (Basis: Version 2026101200), umbenannt zu **`block_completion_progress_tgm`**, damit er parallel zum
  Original installiert werden kann.
- Zielsystem: **Moodle 5.1** (`$plugin->requires` = 4.5, `supported` = 405–503).
- Produktivserver: Moodle-Code in `/srv/moodle5`, Web-Root `public/`.
  - Plugin: `/srv/moodle5/public/blocks/completion_progress_tgm` (Git-Checkout von Branch `tgm`)
  - CLI-Skripte liegen **außerhalb** von `public/`: `/srv/moodle5/admin/cli/…`

### Erweiterungen gegenüber dem Original

1. **Graue Felder** für (noch) nicht erreichbare Aktivitäten
   (`config_showunavailable`, `config_unavailableskipgroups`, Farbe `unavailable_colour`).
2. **Aufgabentypen**: erstes Wort des Aktivitätsnamens (Erarbeiten, Festigen, Üben, ABGABE, Erweitert;
   Admin-Einstellung `tasktypes`, Format `Schlüsselwort | Icon | Bezeichnung`) → Font-Awesome-Icon im Feld,
   Legende, Info-Popup. Logik in `classes/task_types.php`.
3. **Übersicht**: eingeklappte Spalten geben ihre Breite frei (`classes/table/overview.php::setup()`,
   Klasse `collapsedcolumn`).
4. **Anordnung „Gruppiert nach Aufgabentyp“** (`orderbytasktype`): Gruppen mit Abstand, optional
   Gruppenkopf (`config_tasktypeiconpos`: header/cell/both). Logik in
   `completion_progress::sorter_orderbytasktype()` und `build_groups()`, Zell-Markup im Partial
   `templates/progress_cell.mustache`.

## Namenskonventionen nach der Umbenennung

| Was | Name |
|---|---|
| Komponente / Namespace / Blockklasse / CSS-Präfix | `block_completion_progress_tgm` |
| Blockname (`block_instances.blockname`) | `completion_progress_tgm` |
| Capabilities | `block/completion_progress_tgm:*` |
| Cache-Tabelle | `block_completion_tgm` (XMLDB erlaubt max. 28 Zeichen) |
| Kernklasse | `block_completion_progress_tgm\completion_progress` (Klassenname **unverändert**) |
| Restore-Task | `restore_completion_progress_tgm_block_task` |

Achtung: `completion_progress::KONSTANTE` ist die Klasse, **nicht** in `completion_progress_tgm::` umbenennen
(ist bei der Umbenennung einmal passiert).

## Git-Workflow

- **Kein ZIP**, Lieferung immer über Commits in `origin` (= `elist-tgm/moodle-block_completion_progress`).
- `tgm` ist der Auslieferungs-Branch (wird am Server gepullt). `master` = unverändertes Upstream.
- Neue Erweiterungen: Branch `feature/<name>` von `tgm`, pushen, Nutzer testet am Server,
  nach Freigabe Fast-Forward-Merge nach `tgm`. Kleine Bugfixes an bereits freigegebenem Code direkt auf `tgm`.
- Kein Force-Push auf `tgm`.

## Pflicht bei jeder Änderung

- **Versionsnummer** in `version.php` erhöhen (`YYYYMMDDXX`), sobald sich DB, Einstellungen, Templates,
  CSS, JS oder Sprachstrings ändern; `$plugin->release` ergänzen. Reine PHP-Logik braucht kein Upgrade.
- **Sprachdateien**: neue Strings immer in `lang/en/` **und** `lang/de/` eintragen, alphabetisch sortiert.
  `lang/de` ist vollständig (Basis: offizielle AMOS-Übersetzung des Originals) und muss es bleiben –
  Schlüssel-Abgleich en/de prüfen.
- **README.md**: Liste der Erweiterungen aktuell halten.
- In der Antwort an den Nutzer immer die **geänderten Dateien** auflisten und die Befehle für den Server
  angeben (git pull, ggf. Upgrade/Caches).
- Einfüge-Anker bei skriptgesteuerten Edits müssen **eindeutig** sein (zweimal passiert:
  `$greymode = …` kam doppelt vor → Code landete in der falschen Methode). Nach dem Edit mit `grep`
  prüfen, wo der Code gelandet ist.

## Prüfen und Testen

Mindestens (geht auch ohne Moodle):

```bash
for f in $(git ls-files '*.php'); do php -l "$f" >/dev/null || echo "FEHLER: $f"; done
php -r 'define("MOODLE_INTERNAL",1); $string=[]; include "lang/en/block_completion_progress_tgm.php"; $e=array_keys($string);
        $string=[]; include "lang/de/block_completion_progress_tgm.php"; print_r(array_diff($e, array_keys($string)));'
```

Mit Moodle-Testinstanz (z. B. moodle-docker), Plugin unter `public/blocks/completion_progress_tgm`:

```bash
php admin/cli/upgrade.php --non-interactive
php admin/cli/purge_caches.php
vendor/bin/phpunit --testsuite block_completion_progress_tgm_testsuite
php admin/tool/behat/cli/run.php --tags=@block_completion_progress_tgm
npx grunt amd --root=public/blocks/completion_progress_tgm   # nach Änderungen in amd/src
```

JS: `amd/build/*.min.js` werden **nicht** von Hand gepflegt, sondern mit `grunt amd` erzeugt.

Server (als Webserver-Benutzer):

```bash
cd /srv/moodle5/public/blocks/completion_progress_tgm && git pull
sudo -u www-data php /srv/moodle5/admin/cli/upgrade.php --non-interactive
sudo -u www-data php /srv/moodle5/admin/cli/purge_caches.php
```

## Kennzeichnung von KI-Beiträgen

Dieses Repository folgt der Konvention aus der Linux-Kernel-Dokumentation
(`Documentation/process/coding-assistants.rst`), die auch von LLVM, Fedora u. a. verwendet wird:

- Jeder Commit, der ganz oder wesentlich von einem KI-Assistenten erstellt wurde, erhält den Trailer
  ```
  Assisted-by: AGENT_NAME:MODEL_VERSION
  ```
  z. B. `Assisted-by: Claude:claude-opus-5-5`. Zusätzlich darf `Co-Authored-By:` stehen
  (zeigt GitHub als Co-Autor an).
- KI-Assistenten setzen **kein** `Signed-off-by:` – das ist die Bestätigung eines Menschen, der die
  Änderung geprüft hat und verantwortet.
- Verantwortlich für jeden Commit bleibt der Mensch, der ihn übernimmt (Review + Test am Server).
- Im Code selbst keine KI-Kommentare pro Datei; die Zuordnung erfolgt über die Git-Historie
  (`git log --format='%h %s %(trailers:key=Assisted-by,valueonly)'`) und den Abschnitt in `README.md`.
