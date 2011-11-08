=== CSV to SortTable ===

Contributors: sscovil
Tags: data, table, csv, import, sort, sortable, sorttable
Requires at least: 3.2.1
Tested up to: 3.2.1
Stable tag: 1.0

This plugin allows you to import data from a CSV file and display it in a sortable table using a simple shortcode.

== Description ==

This plugin allows you to import data from a CSV file and display it in a sortable table using a simple shortcode.

The sortable table portion of this plugin is made possible by Stuart Langridge's awesome Javascript library: sorttable.js.

Documentation for sorttable.js can be found at: http://www.kryogenix.org/code/browser/sorttable/

== Installation ==

1. Upload the entire `csv2sorttable` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Insert the shortcode `[csv2table source="http://mydomain.com/mydatafile.csv"]` in any post or page

== Frequently Asked Questions ==

= Why is my data not sorting correctly? =

By default, this plugin treats each sortable column as a text string. If you need to treat data in  a particular column
as a number or a date, you will need to modify your shortcode like this:

`[csv2table source="http://mydomain.com/mydatafile.csv" number="2,3,4" date="5,6"]`

Use a comma-separated list of column numbers to indicate which columns should be treated as `number` or `date` for
sorting purposes. For example, the first column in your .csv file would be 1, the second would be 2, and so on.

= How do I specify which columns should not be sortable? =

Modify your shortcode like this, using a comma-separated list of column numbers:

`[csv2table source="http://mydomain.com/mydatafile.csv" unsortable="1,6,7"]`

= How do I style my sortable tables? =

A: Below is the code I used to change the default colors to match my theme on this site. You can add similar code to your theme's `style.css`. 

The first section changes the background and text color of the header row. The next section changes the background color of every even row in the table. I also changed the color of the cell borders to match the header row. Then I used a lighter color to highlight a sortable column head on hover. And finally, I specified column widths using percentages for the last five columns.

`table.sortable thead tr {
	background-color: #71a7c8 !important;
	color: #fff !important;
}

table.sortable tr:nth-child(even) { background: #f6f6f6 !important; }

table.sortable th,
table.sortable td {
	border: 1px solid #71a7c8 !important;
}

table.sortable th:hover:not(.sorttable_nosort) {
	background: #b3d0e1 !important;
}

td.col3, td.col4, td.col5, td.col6, td.col7 { text-align: center !important; width: 10% !important; }`

== Changelog ==

= 1.0 =
* First public release.