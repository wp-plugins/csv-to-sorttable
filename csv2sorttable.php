<?php
/*
Plugin Name: CSV to SortTable
Plugin URI: http://mynewsitepreview.com/csv2sorttable
Description: Import data from a CSV file and display it in a sortable table using a simple shortcode.
Version: 2.1.1
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

	$opt_source =  $args['source']; // URL of the .csv file to import
	$opt_group = $args['group']; // 
	$opt_unsortable = explode(",", $args['unsortable']); // Column numbers that should not be sortable
	$opt_numeric = explode(",", $args['number']); // Column numbers that should be treated as numbers when sorting
	$opt_date = explode(",", $args['date']); // Column numbers that should be treated as dates (mmdd) when sorting
	
	if( $opt_group ) {
		$group = 0; // A common class will be assigned to adjacent rows with matching content
		$prev_cleancontent = ''; // Store content from the previous cell, to compare when grouping rows
		$evenodd = 'even';
	}
	
	// This will be used to check for URLs within table cell data
	$email_pattern = "/[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}/i";
	$url_pattern = "/((http|https|ftp|sftp):\/\/)[a-z0-9\-\._]+\/?[a-z0-9_\.\-\?\+\/~=&#;,]*[a-z0-9\/]{1}/si";
	$www_pattern = "/(www)[a-z0-9\-\._]+\/?[a-z0-9_\.\-\?\+\/~=&#;,]*[a-z0-9\/]{1}/si";
	
	// If the source of the .csv file is valid...
	if (($handle = fopen($opt_source, "r")) !== FALSE) {
	
		// ...begin recording echos as an output string
		ob_start();
		echo '<table class="sortable">';	

		// Begin the loop to generate the table header row and body rows
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$num = count($data); // Number of columns in the table

			// Initialize variables that will be echoed as a string at the end of each loop
			if( $opt_group ) {
				$tr_start = '<tr';
			} else {
				$tr_start = '<tr>';
			}
			$tr_class = '';
			$tr_mid = '';
			$tr_end = '</tr>';
			
			for ($c=0; $c < $num; $c++) {
				$col = $c + 1; // Used to give each column a unique class
				// Clean up the raw .csv content by converting special characters to HTML
				$cleancontent = htmlentities($data[$c], ENT_QUOTES, "ISO-8859-1");
				$cleancontent = str_replace( 
					array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
					array("'", "'", '"', '"', '-', '--', '...'),
					$cleancontent
				);

				// Check .csv table data to see if the cell contains an email address, proper URL, or www address
				$cleancontent = findlinks($cleancontent);
				
				// For grouping columns, if the option is set
				if( $opt_group && $col == $opt_group ) { // If this is the chosen grouping column...
					if ( $row == 0 ) { // ...and it is the header row...
						$tr_class = '>'; // ...then just close the <tr> tag.
					} else { // Otherwise, we are in the table body...
						if( !($cleancontent == $prev_cleancontent) ) { // ...so check the contents of the row above...
							$group++; // ...and if they don't match, begin the next group.
							$prev_cleancontent = $cleancontent; // Then store the current cell contents as 'previous'.
							if( $evenodd == 'even' ) { $evenodd = 'odd'; } else { $evenodd = 'even'; }
						}
						$tr_class = ' class="group' . $group . ' ' . $evenodd . '">'; // Assign the class 'groupX' to the <tr>
					}
				}
				// Create the <th> and <td> cells
				if ($row == 0) { // Cell is in header row <th>
					if (in_array($col, $opt_unsortable)) {
						// The user defined this column as 'unsortable'
						$addclass = 'sorttable_nosort';
					} elseif (in_array($col, $opt_numeric)) {
						// The user defined this column as 'number'
						$addclass = 'sorttable_numeric';
					} elseif (in_array($col, $opt_date)) {
						// The user defined this column as 'date' (format: mmdd)
						$addclass = 'sorttable_mmdd';
					} else {
						// By default, data in columns is sortable alphabetically
						$addclass = 'sorttable_alpha';
					}
					$addclass .= ' col' . $col; // Each column gets a unique classe for styling column widths, etc.
					$tr_mid .= '<th class="' . $addclass . '">' . $cleancontent . '</th>';
				} else { // Cell is in body row <td>
					$tr_mid .= '<td class="col' . $col . '">' . $cleancontent . '</td>';
				}
			}
			echo $tr_start . $tr_class . $tr_mid . $tr_end;
			$row++;
		}
		// End of loop

		echo '</table>';
		// End of table

		fclose($handle);
		$content = ob_get_contents();;
		ob_end_clean();
		return $content;
	}
}
add_shortcode("csv2table", "csv2sorttable");

function findlinks($text) {
        $email_pattern = "/[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}/i";
        $url_pattern = "/((http|https|ftp|sftp):\/\/)[a-z0-9\-\._]+\/?[a-z0-9_\.\-\?\+\/~=&#;,]*[a-z0-9\/]{1}/si";
        $www_pattern = "/(www)[a-z0-9\-\._]+\/?[a-z0-9_\.\-\?\+\/~=&#;,]*[a-z0-9\/]{1}/si";
 
        // First, check if the string contains an email address...
        if( preg_match( $email_pattern, $text, $email ) ) {
                $replacement = '<a href="mailto:' . $email[0]. '">' . $email[0] . '</a> ';
                $text = preg_replace($email_pattern, $replacement, $text);
        }
        // Next, check if the string contains a URL beginning with http://, https://, ftp://, or sftp://
        // ...and if not, check for a plain old www address
        if( preg_match( $url_pattern, $text, $url ) ) {
                $replacement = '<a href="' . $url[0]. '">' . $url[0] . '</a> ';
                $text = preg_replace($url_pattern, $replacement, $text);
        } elseif( preg_match( $www_pattern, $text, $www ) ) {
                $replacement = '<a href="http://' . $www[0]. '">' . $www[0] . '</a> ';
                $text = preg_replace($www_pattern, $replacement, $text);
        }
 
        return $text; 
}
?>