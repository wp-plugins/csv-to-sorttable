<?php
/*
Plugin Name: CSV to SortTable
Plugin URI: http://mynewsitepreview.com/csv2sorttable
Description: Import data from a CSV file and display it in a sortable table using a simple shortcode.
Version: 1.0
Author: Shaun Scovil
Author URI: http://shaunscovil.com/
License: GPL2
*/

/*  Copyright 2011  Shaun Scovil  (email : sscovil@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Load plugin stylesheet, as well as sorttable.js by Stuart Langridge
if ( !is_admin() ) {
	function csv2sorttable_enqueue_scripts() { 
		wp_enqueue_script( 'sorttable', plugins_url( '/scripts/sorttable.js', __FILE__ ) );
	} 
	add_action('wp_enqueue_scripts', 'csv2sorttable_enqueue_scripts'); 
	function csv2sorttable_enqueue_styles() {
		$myStyleUrl = plugins_url('/css/csv2sorttable.css', __FILE__);
		wp_register_style('csv2sorttable', $myStyleUrl);
		wp_enqueue_style( 'csv2sorttable');
	}
	add_action('wp_print_styles', 'csv2sorttable_enqueue_styles');
}

// Import .csv and output data in a sortable table
function csv2sorttable($args){

	$source =  $args['source']; // URL of the .csv file to import
	$unsortable = explode(",", $args['unsortable']);
	$numeric = explode(",", $args['number']);
	$date = explode(",", $args['date']);
	
	if (($handle = fopen($source, "r")) !== FALSE) {
	
		ob_start();
		
		echo '<table class="sortable">';	
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$num = count($data);
			echo '<tr>';
			for ($c=0; $c < $num; $c++) {
				$col = $c + 1; // Used to give each column a unique class
				$cleancontent = htmlentities($data[$c], ENT_QUOTES, "ISO-8859-1"); // Convert special characters to HTML
				$cleancontent = str_replace( // Convert special characters that htmlentities() misses, like the ellipsis 
					array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
					array("'", "'", '"', '"', '-', '--', '...'),
					$cleancontent);
				if ($row == 0) {
					if (in_array($col, $unsortable)) {
						// The user defined this column as 'unsortable'
						$addclass = "sorttable_nosort";
					} elseif (in_array($col, $numeric)) {
						// The user defined this column as 'number'
						$addclass = "sorttable_numeric";
					} elseif (in_array($col, $date)) {
						// The user defined this column as 'date' (format: mmdd)
						$addclass = "sorttable_mmdd";
					} else {
						// By default, data in columns is sortable alphabetically
						$addclass = "sorttable_alpha";
					}
					$addclass .= " col" . $col; // Each column gets a unique classe for styling column widths, etc.
					echo "<th class='" . $addclass . "'>" . $cleancontent . "</th>";
				} else {
					echo "<td class='col" . $col . "'>" . $cleancontent . "</td>";
				}
			}
			echo '</tr>';
			$row++;
		}
		echo '</table>';

		fclose($handle);
		$content = ob_get_contents();;
		ob_end_clean();
		return $content;
	}
}

// Add shortcode
add_shortcode("csv2table", "csv2sorttable");

?>