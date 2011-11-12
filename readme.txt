=== CSV to SortTable ===

Contributors: sscovil
Tags: data, table, csv, import, sort, sortable, sorttable
Requires at least: 3.2.1
Tested up to: 3.2.1
Stable tag: 2.1

This plugin allows you to import data from a spreadsheet (.csv file format) and display it in a sortable table using a simple shortcode.

== Description ==

Necessity is the mother of invention. In addition to building websites, I run a weekly game night at a local pub near Boston. One of the biggest draws is the popular music game 'RockBand', and I have ~1,200 songs for folks to choose from. I have a spreadsheet with all of my songs sorted by artist, but people have been asking me to put it online so they can request new songs from home. The solution was this plugin!

CSV to SortTable is great for anyone who keeps track of important information using a spreadsheet. It could be used for product catalogs, inventory lists, or even leaderboards in a competition.

**How To Use**

Add the contents of a .csv file by placing this shortcode into your post or page:

`[csv2table source="http://mydomain.com/mydatafile.csv"]`

Be sure to use the absolute URL (including 'http://') when entering the location of your spreadsheet.

**Make Certain Columns Unsortable**

`[csv2table source="http://mydomain.com/mydatafile.csv" unsortable="1,6,7"]`

Use a comma-separated list of column numbers to indicate which columns should be treated as unsortable.

**Sort Certain Columns As Numbers or Dates**

`[csv2table source="http://mydomain.com/mydatafile.csv" number="2,3,4" date="5,6"]`

Use a comma-separated list of column numbers to indicate which columns should be treated as numbers or dates
for sorting purposes.

**Group Certain Rows by Column Value**

As of version 2.0 if you specify a `group` column, the plugin will automatically assign a common class to all adjacent rows containing the same data in a specified column.

`[csv2table source="http://mydomain.com/mydatafile.csv" group="1"]`

You can only assign one `group` column and your .csv file should already be sorted by that column in advance.

**About This Plugin**

For more information about this plugin, visit: http://mynewsitepreview.com/csv2sorttable/

To see a live demo, visit: http://mynewsitepreview.com/csv2sorttable-wordpress-plugin-live-demo

**About SortTable.js**

The sortable table portion of this plugin is made possible by Stuart Langridge's awesome Javascript library.

Documentation for sorttable.js can be found at: http://www.kryogenix.org/code/browser/sorttable/

== Installation ==

1. Upload the entire folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Insert the shortcode `[csv2table source="http://mydomain.com/mydatafile.csv"]` in any post or page

== Frequently Asked Questions ==

= How do I style my sortable tables? =

I know, I know. The default colors look pretty nasty in most themes. Also, you may want to style individual columns. Below is the code I used to change the default colors to match my theme on the live demo seen here: http://mynewsitepreview.com/csv2sorttable-wordpress-plugin-live-demo

You can add similar code to your theme's `style.css`:

`/* Header Row Colors */
table.sortable thead tr {
	background-color: #71a7c8 !important;
	color: #fff !important;
}

/*  Highlight Color for Header Row Cells on Hover*/
table.sortable th:hover:not(.sorttable_nosort) {
	background: #b3d0e1 !important;
}

/* Shading For Even Rows */
table.sortable tr:nth-child(even) { background: #f6f6f6 !important; }

/* Table Border Color */
table.sortable th,
table.sortable td {
	border: 1px solid #71a7c8 !important;
}

/*  Style & Width of Particular Columns */
table.sortable td.col3,
table.sortable td.col4,
table.sortable td.col5,
table.sortable td.col6,
table.sortable td.col7 {
	text-align: center !important;
	width: 10% !important;
}`

== Changelog ==

= 2.1 =
* Fixed problem with URLs getting truncated when converted to links.
* Now correcly converts email and www addresses to `mailto:` and `http://` links, respectively.

= 2.0 =
* Automatically detects URLs contained in cells and converts them into HTML links.
* Added `group` option, which assigns a unique common class to all adjacent rows containing the same data in the specified column.
* Added `even` and `odd` classes to row groups.

= 1.0 =
* First public release.