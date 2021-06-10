=== Xirki Customizer Framework ===
Contributors: aristath, dannycooper, wplemon, igmoweb
Tags: customizer, options framework, theme, mods, toolkit, gutenberg
Donate link: https://xplodedthemes.com/donate
Requires at least: 4.9
Tested up to: 5.2
Stable tag: 3.0.45
License: MIT
License URI: https://opensource.org/licenses/MIT

The ultimate framework for theme developers using the WordPress Customizer


== Description ==

[![Build Status](https://travis-ci.org/xplodedthemes/xirki.svg?branch=develop)](https://travis-ci.org/xplodedthemes/xirki) [![Code Climate](https://codeclimate.com/github/xplodedthemes/xirki/badges/gpa.svg)](https://codeclimate.com/github/xplodedthemes/xirki) [![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/66d6d8b6a4654cd18686ed1cd9f1bfb3)](https://www.codacy.com/app/xplodedthemes/xirki?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=xplodedthemes/xirki&amp;utm_campaign=Badge_Grade) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/xplodedthemes/xirki/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/xplodedthemes/xirki/?branch=develop)

Using Xirki theme developers can create rich experiences for the WordPress Customizer using best coding practices.

Included are more than 30 custom control types ranging from simple sliders to complex typography controls with Google-Fonts integration, automatic CSS generation, `postMessage` scripts automatically generated, tooltips and a lot of extras that make developing themes a lot faster for developers and meaningful for users.

### Control Types ###

* [Background Customizer Control](https://xplodedthemes.com/docs/controls/background)
* [Checkbox Customizer Control](https://xplodedthemes.com/docs/controls/checkbox)
* [Code Customizer Control](https://xplodedthemes.com/docs/controls/code)
* [Color Palette Customizer Control](https://xplodedthemes.com/docs/controls/color-palette)
* [Color Customizer Control](https://xplodedthemes.com/docs/controls/color)
* [Custom Customizer Control](https://xplodedthemes.com/docs/controls/custom)
* [Dashicons Customizer Control](https://xplodedthemes.com/docs/controls/dashicons)
* [Date Customizer Control](https://xplodedthemes.com/docs/controls/date)
* [Dropdown Pages Customizer Control](https://xplodedthemes.com/docs/controls/dropdown-pages)
* [Editor Customizer Control](https://xplodedthemes.com/docs/controls/editor)
* [Generic Customizer Control](https://xplodedthemes.com/docs/controls/generic)
* [Image Customizer Control](https://xplodedthemes.com/docs/controls/image)
* [Link Customizer Control](https://xplodedthemes.com/docs/controls/link)
* [Multiple Checkbox Customizer Control](https://xplodedthemes.com/docs/controls/multicheck)
* [Multicolor Customizer Control](https://xplodedthemes.com/docs/controls/multicolor)
* [Number Customizer Control](https://xplodedthemes.com/docs/controls/number)
* [Radio Buttonset Customizer Control](https://xplodedthemes.com/docs/controls/radio-buttonset)
* [Radio Image Customizer Control](https://xplodedthemes.com/docs/controls/radio-image)
* [Radio Customizer Control](https://xplodedthemes.com/docs/controls/radio)
* [Repeater Customizer Control](https://xplodedthemes.com/docs/controls/repeater)
* [Select Customizer Control](https://xplodedthemes.com/docs/controls/select)
* [Slider Customizer Control](https://xplodedthemes.com/docs/controls/slider)
* [Sortable Customizer Control](https://xplodedthemes.com/docs/controls/sortable)
* [Spacing Customizer Control](https://xplodedthemes.com/docs/controls/spacing)
* [Switch Customizer Control](https://xplodedthemes.com/docs/controls/switch)
* [Text Customizer Control](https://xplodedthemes.com/docs/controls/text)
* [Textarea Customizer Control](https://xplodedthemes.com/docs/controls/textarea)
* [Toggle Customizer Control](https://xplodedthemes.com/docs/controls/toggle)
* [Typography Customizer Control](https://xplodedthemes.com/docs/controls/typography)
* [Upload Customizer Control](https://xplodedthemes.com/docs/controls/upload)

Premium controls are also available for premium themes:

* [Xirki WCAG Text Colorpicker](https://wplemon.com/downloads/xirki-wcag-text-colorpicker/)
* [Xirki WCAG Links Colorpicker](https://wplemon.com/downloads/xirki-wcag-link-colorpicker/)
* [Xirki Box Model](https://wplemon.com/downloads/xirki-box-model/)
* [Xirki Box Shadow](https://wplemon.com/downloads/xirki-box-shadow/)

All premium controls are also [available as a pack](https://wplemon.com/downloads/xirki-premium-controls-pack/)

Theme developers should be familiar with the Customizer API before you start writing your theme using Xirki. An excellent handbook for the WordPress Customizer can be found on the [developer.wordpress.org](https://developer.wordpress.org/themes/customize-api/) website.

You can find detailed documentation on how to use Xirki on [xirki.org](https://xplodedthemes.com)

[Development and issues on Github](https://xplodedthemes.com).

== Installation ==

Simply install as a normal WordPress plugin and activate.

If you want to integrate Xirki in your theme or plugin, please read the instructions on [our documentation site](https://xplodedthemes.com/docs/integration).

== Changelog ==

= 3.0.45 - 2019-09-01 =

### Fixed
* Compatibility with the latest Gutenberg plugin versions.
* Styles for normal font-weights in typography controls (`regular` to `400` conversion).

### Changed
* Updated Google-Fonts lists.

### Removed
* Backup fonts are no longer needed since google-fonts are no longer loaded from the google CDN.

### Deprecated
* `xirki_fonts_backup_fonts` filter.
* `Xirki_Fonts::get_backup_fonts()` method.

[See the previous changelogs here](https://xplodedthemes.com/blob/master/CHANGELOG.md).
