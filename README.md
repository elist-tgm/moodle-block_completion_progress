# Completion Progress block (TGM fork)

Fork of [block_completion_progress](https://github.com/jonof/moodle-block_completion_progress),
renamed to `block_completion_progress_tgm` so it can be installed alongside the original.
Install into `blocks/completion_progress_tgm` (Moodle 5.1+: `public/blocks/completion_progress_tgm`).
Cache table: `block_completion_tgm`.

Extensions:
- (Not yet) accessible activities are shown as grey cells (block setting).
- Task types (Erarbeiten, Festigen, Üben, ABGABE, Erweitert, configurable) detected from the first word of the activity name and shown as icons in the bar, with legend.
- Ordering option 'Grouped by task type' with optional group headers.
- Overview: collapsed columns release their width.
- Activities hidden from students are not shown in the bar; block setting to show them to teachers.

## AI-assisted development

The extensions of this fork were developed with the help of an AI coding assistant
(Claude, Anthropic). All changes were reviewed and tested by the maintainer before release.
Commits created with AI assistance carry an `Assisted-by:` trailer, following the convention of the
Linux kernel documentation (*AI Coding Assistants*). List them with:

    git log --format='%h %s %(trailers:key=Assisted-by,valueonly)'

Commits before this convention was introduced carry a `Co-Authored-By: Claude …` trailer instead.
Notes for AI assistants working on this repository are in `CLAUDE.md`.

# Completion Progress block for Moodle

The Completion Progress block is a time-management tool for students. It visually shows what activities/resources a student is supposed to interact with in a course. It is colour-coded so students can quickly see what they have and have not completed/viewed. The block shows activities with activity completion settings.

To install, please refer to [the Moodle documentation for installing plugins](https://docs.moodle.org/en/Installing_plugins#Installing_a_plugin) appropriate to your Moodle version.

Once the Completion Progress block is installed, you can use it in a course as follows.

1. Turn editing on
2. Create your activities/resources as normal
3. Set completion settings for each activity you want to appear in the bar, including an expected by date
4. Add the Completion Progress block to your page
5. Move your block into a prominent position
6. (Optional) Configure how the block should appear

Hidden items will not appear in the Completion Progress block until they are visible to students. This is useful for a scheduled release of activities.
