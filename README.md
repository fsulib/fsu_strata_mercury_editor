# FSU Strata Mercury Editor
Customizations to Mercury Editor for Florida State University's Strata Drupal theme. This includes entities, layouts, style options and UI customization.

This module was developed by [Aten Design Group](https://aten.io) for [Florida State University Libraries](https://www.lib.fsu.edu/).

## Installation

Add the following repos to your list of repositories in `composer.json`

```
"repositories": [
    ...
    {
        "type": "git",
        "url": "https://bitbucket.org/atendesigngroup/mercury_editor.git"
    },
    {
        "type": "git",
        "url": "https://bitbucket.org/atendesigngroup/fsu_strata_mercury_editor.git"
    }
],
```

Add the following to the list of patches in `composer.json`. Note: This won't be be required once https://www.drupal.org/project/style_options/issues/3288062 has been committed.
```
"patches": {
    "drupal/style_options": {
        "CSSClass option plugin fails to build if there are no options defined": "https://git.drupalcode.org/project/style_options/-/merge_requests/3.patch"
    }
}
```

Then run the following composer command.
```
composer require atendesigngroup/fsu_strata_mercury_editor:dev-main --with-all-dependencies
```

Then install the module as normal.
```
drush en fsu_strata_mercury_editor -y
```

## Configuration
This module includes the configuration for a content type, a few [Paragraphs](https://www.drupal.org/project/paragraphs) and their related fields and image styles which serve as a starting point for creating content. This configuration is located in the `/config/optional` folder and will be automatically imported when the module is installed.

Due to the nature of Drupal's configuration import system, once the module is installed on a site, you will need to follow the standard site-wide configuration export and import workflow to make edits.

Future modifications to the config files in `config/optional` will not override existing configuration during the normal config import process, but can be imported with the following drush command.

```
drush cim -y --partial --source=modules/contrib/fsu_strata_mercury_editor/config/optional
```
## Enhanced Page
This module includes the configuration for the `Enhanced Page` content type. This content type includes a Paragraph field called `field_content`. The settings for this field control which Paragraphs are available to content editors when authoring an Enhanced Page.

## Paragraphs
This module includes configuration for a few basic Paragraphs types – a.k.a Components. See `/admin/structure/paragraphs_type`. At the time of this writing, this module contains two general Paragraph types, `Text` and `Image`, two FSU Strata specific types, `Hero` and `Card` and one Layout Paragraphs type called `Section`.

The `Section` Paragraph type, which has no fields itself, allows content editors to add a section, or row, to a page and choose a corresponding layout.  Once a `Section` is added, newly created Paragraphs can be added to different regions within the layout of that `Section`. An `Enhanced Page` can contain multiple sections with varied layouts.

## Layouts
Custom layouts are registered in `fsu_strata_mercury_editor.layout.yml`. Each layout's Twig template, and any required CSS and JS should be saved in a corresponding folder within `/layouts`. See [How to register layouts](https://www.drupal.org/docs/drupal-apis/layout-api/how-to-register-layouts) for more information about available options. In order to make use of the [Style Options](https://www.drupal.org/project/style_options) module in your layout, you must explicitly define its `class` property as `\Drupal\style_options\Plugin\Layout\StyleOptionLayoutPlugin`.

In order for newly created layout to appear as an option for content editors, you must enable it on the `Section` paragraph at `/admin/structure/paragraphs_type/section`

## Style Options
Mercury Editor uses the [Style Options](https://www.drupal.org/project/style_options) module to provide flexibility for content editors when authoring content.

Style Options are defined in `fsu_strata_mercury_editor.style_options.yml`. To add a new style option set, you first define the general set of options, or choices an editor can make. You then declare the context (currently Paragraph, Layout and/or Region) in which a given option should be available. For example, you might want the Custom Class option, which allows content editors to input custom CSS classes that will be added to an element, available by default on all Paragraphs, Layouts and Regions. However the Hero Style options, which is very specific to the Hero component, should only be available when editing Hero paragraphs.

How the chosen value from a Style Option gets applied to a Paragraph, Layout or Region is dictated by the corresponding `StyleOption` plugin. The Style Options module provides a couple useful plugins out of the box, such as `CSS Class` and `Background Image`. This module includes additional `StyleOption` plugins developed specifically for FSU.

The first is `Property`. This simply passes the corresponding value onto the theme system for use within preprocess functions or Twig templates. This is useful in cases where a single CSS Class isn't sufficient enough to achieve desired style. For example, the Card Style requires alternative markup for adding the Point or Arrow effects.
See `/src/Plugin/StyleOption/Property.php`

The second is `ImageStyle`. This allows content editors to choose which aspect ratio an image should appear in. This plugin will render the source image in the corresponding image style.
See `/src/Plugin/StyleOption/ImageStyle.php`

## Image styles
This module includes configuration for additional aspect ratio specific images styles as defined by the [FSU Style Guidelines](https://webstyle.unicomm.fsu.edu/3.3/images/). For scaled image styles the naming convention is `Max 1200x800` which means an image should be scaled down so it is no more than `1200px` wide and `800px` tall – without cropping.

For cropped images, these image styles follow a naming convention of `[x]:[y] [width]x[height]`. For example `1:1 180x180` is a square image that is `180px` by `180px`. While this module is not currently configured to use responsive images, we've found following this naming convention to be very when implementing them.

Additionally, all the cropped image styles make use of the [Image Widget Crop](https://www.drupal.org/project/image_widget_crop) module. This module allows content editors to upload any image then interactively specify how it should be cropped at different aspect ratios.

## Theming
Since the intention of this module is to make it work with the existing `Strata` theme, very little custom CSS is required for layouts and components. However, we do need to heavily control the markup via Twig templates. In order to keep this functionality modular and avoid making edits to the `Strata` theme, we need the ability to override `Paragraph` templates in this module. To do so, we must define our own custom theme hooks for specific Paragraphs. See `fsu_strata_mercury_editor_theme()` in `fsu_strata_mercury_editor.module` for more information.

## Permissions
There are additional user permissions related to this module. Since permissions configuration is managed at the user role level, and the role config files contain information that is outside the scope of this module, we must grant permissions programmatically as this module is installed. See `fsu_strata_mercury_editor_install()` in `fsu_strata_mercury_editor.module` for more information. In order for any changes to these permissions to take effect, you must uninstall and reinstall this module.
