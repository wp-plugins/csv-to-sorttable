=== CSV to SortTable ===

Contributors: sscovil
Tags: data, table, csv, import, sort, sortable, sorttable
Requires at least: 3.2.1
Tested up to: 3.3.1
Stable tag: 3.1

This plugin allows you to import data from a spreadsheet (.csv file format) and display it in a sortable table using a simple shortcode.

== Description ==

Necessity is the mother of invention. In addition to building websites, I run a weekly game night at a local pub near Boston. One of the biggest draws is the popular music game 'RockBand', and I have ~1,200 songs for folks to choose from. I have a spreadsheet with all of my songs sorted by artist, but people have been asking me to put it online so they can request new songs from home. The solution was this plugin!

CSV to SortTable is great for anyone who keeps track of important information using a spreadsheet. It could be used for product catalogs, inventory lists, or even leaderboards in a competition.

**How To Use**

Add the contents of a .csv file by placing this shortcode into your post or page.

`[csv2table source="http://mydomain.com/mydatafile.csv"]`

**Additional Shortcode Parameters**

Use a comma-separated list of column numbers to indicate which columns should be treated as unsortable.

`[csv2table source="http://mydomain.com/mydatafile.csv" unsortable="1,6,7"]`

Use a comma-separated list of column numbers to indicate which columns should be treated as numbers or dates for sorting purposes.

`[csv2table source="http://mydomain.com/mydatafile.csv" number="2,3,4" date="5,6"]`

As of version 2.0 if you specify a `group` column, the plugin will automatically assign a common CSS class to all adjacent rows that contain the same data in the specified column.

`[csv2table source="http://mydomain.com/mydatafile.csv" group="1"]`

You can only assign one `group` column and your .csv file should already be sorted by that column in advance.

`[csv2table source="http://mydomain.com/mydatafile.csv" icons="true"]`

NEW (since v3.1): You can automatically replace certain file urls with special file-type icons.

**About This Plugin**

For more information about this plugin, visit: http://mynewsitepreview.com/csv2sorttable/

To see a live demo, visit: http://mynewsitepreview.com/csv2sorttable-wordpress-plugin-live-demo/

**Credit**

The sortable table portion of this plugin is made possible by Stuart Langridge's awesome Javascript library.

Documentation for sorttable.js can be found at: http://www.kryogenix.org/code/browser/sorttable/

== Installation ==

1. Upload the entire folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Insert the shortcode `[csv2table source="http://mydomain.com/mydatafile.csv"]` in any post or page

== Frequently Asked Questions ==

= How do I style my sortable tables? =

Add the following code to your theme's `style.css`, replacing the color codes and other styles as you see fit. Be sure to use `!important` to override the default plugin styles!

`/* Header Row Colors */
table.sortable thead tr {
	background-color: #f2f2f2 !important;
	color: #1A569F !important;
}

/*  Highlight Color for Header Row Cells on Hover*/
table.sortable th:hover:not(.sorttable_nosort) {
	background: #d2d2d2 !important;
}

/* Shading For Even Rows */
table.sortable tr:nth-child(even) { background: #f8f8f8 !important; }

/* Table Border Color */
table.sortable th,
table.sortable td {
	border: 1px solid #d5d5d5 !important;
}`

You can also style individual columns to control column widths, text alignment, etc. by using CSS like this:

`/*  Style for Columns 3 through 7 */
table.sortable td.col3,
table.sortable td.col4,
table.sortable td.col5,
table.sortable td.col6,
table.sortable td.col7 {
	text-align: center !important;
	width: 10% !important;
}`

== Changelog ==

= 3.1 =
* Fixed bug that was adding td .col class without column number (i.e. class was 'col' instead of 'col1', 'col2', etc.)
* Added `icons` shortcode parameter to replace url links for certain file types (e.g. PDF, MP3, MOV) with file-type icons
* Renamed functions using the mnsp_ prefix
* Cleaned up code

= 3.0 =
* Replaced fopen() function with curl for retrieving .csv data
* Added mnsp_parse_csv() function to replace fgetcsv(), which requires fopen() -- str_getcsv() would have worked with curl, but requires PHP v5.3
* Changed the default CSS to a nicer light blue theme
* Cleaned up code

= 2.1.1 =
* Cleaned up code by creating a separate function for finding links in cell data.

= 2.1 =
* Fixed problem with URLs getting truncated when converted to links.
* Now correcly converts email and www addresses to `mailto:` and `http://` links, respectively.

= 2.0 =
* Automatically detects URLs contained in cells and converts them into HTML links.
* Added `group` option, which assigns a unique common class to all adjacent rows containing the same data in the specified column.
* Added `even` and `odd` classes to row groups.

= 1.0 =
* First public release.