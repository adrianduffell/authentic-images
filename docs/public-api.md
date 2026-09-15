# Public API

Authentic Images implements a stable public API intended for use by third-party code. All items listed here increment the plugin's MAJOR semver version when any known incompatible changes are made.

## Hooks

## Script and style handles

These handles are registered by the plugin and can be used as dependencies in third-party enqueues.

### Styles

#### `authenticimages-classic-badge`

Front-end badge stylesheet for classic (non-block) themes. Registered—but not automatically enqueued—on `wp_enqueue_scripts`. Use `wp_enqueue_style( 'authenticimages-classic-badge' )` or declare it as a dependency to load it on demand. Added in 1.0.0.

#### `authenticimages-classic-message`

Front-end message stylesheet for classic (non-block) themes. Registered—but not automatically enqueued—on `wp_enqueue_scripts`. Use `wp_enqueue_style( 'authenticimages-classic-message' )` or declare it as a dependency to load it on demand. Added in 1.0.0.

#### `authenticimages-classic-notice`

Front-end notice stylesheet for classic (non-block) themes. Registered on `wp_enqueue_scripts` and automatically enqueued when the notice is rendered. Added in 1.0.0.

#### `authenticimages-admin`

Admin stylesheet enqueued on all `admin_enqueue_scripts` pages. Added in 1.0.0.

### Scripts

#### `authenticimages-editor`

Block editor JavaScript enqueued on `enqueue_block_editor_assets`. Contains the block editor integration for the notice, badge, and message blocks. Added in 1.0.0.

#### `authenticimages-admin-canvas-scripts`

Admin JavaScript enqueued on `enqueue_block_assets` in wp-admin for editor canvas previewing. Added in 1.0.0.

## CSS classes

These classes are part of the public API and stable across versions. They can be targeted for custom styling.

### Front-end classes

#### `.authenticimages-notice`

Applied to the classic Authentic Image Notice container. Added in 1.0.0.

#### `.authenticimages-badge`

Applied to the badge element. Used by both the block renderer and classic theme template hooks. Added in 1.0.0.

#### `.authenticimages-message`

Applied to the message element. Used by both the block renderer and classic theme template hooks. Added in 1.0.0.

## Blocks

### `authenticimages/authentic-image-notice`

Container to group the badge and message. The default layout is horizontal with a `0.33em` block gap, and the badge and message appearing side-by-side. Added in 1.0.0.

### `authenticimages/authentic-badge`

Displays the badge within the notice parent block on product pages. Added in 1.0.0.

Styles are inherited from site-wide settings. Default style values:

| Property           | Default          |
| ------------------ | ---------------- |
| `width`            | `fit-content`    |
| `text-box-trim`    | `trim-both`      |
| `text-box-edge`    | `cap alphabetic` |
| `line-height`      | `1`              |
| `background-color` | `#FFEE85`\*      |
| `color`            | `#111111`\*      |
| `padding`          | Calculated\*¹    |
| `font-size`        | Calculated\*¹    |
| `font-weight`      | `600`\*          |
| `border-radius`    | `2px`\*          |

1. The default height of the badge is 1.66x the capital letter height of surrounding text.

- The default padding on each side is 25% the height of the badge.
- The default font-size is 50% the height of the badge.

Use the scale setting to control the height of the badge, and density (called "font-size" in the UI) to control the font-size/padding ratio.

\* Denotes modifiable in settings.

### `authenticimages/authentic-message`

Displays the message within the notice parent block on product pages. Added in 1.0.0.

| Attribute  | Type     | Default | Description                                |
| ---------- | -------- | ------- | ------------------------------------------ |
| `fontSize` | `string` | `small` | Text size preset (e.g. `small`, `medium`). |

## Non-Public API

The following items are intentionally excluded from the public API. They may change at any time without a MAJOR version bump. Do not rely on them in third-party code.

- All code items tagged with `@internal` comment.

  These are intended only for use internally and are likely to change in refactors.

- File paths.

  File paths are subject to change in future versions.

- Admin dashboard CSS.

  Admin dashboard-related selectors and HTML structures are subject to change.
