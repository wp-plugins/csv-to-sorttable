<?php
/*
Plugin Name: CSV to SortTable
Plugin URI: http://mynewsitepreview.com/csv2sorttable
Description: Import data from a CSV file and display it in a sortable table using a simple shortcode.
Version: 3.1
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



// Enqueue scripts and stylesheets
if ( !is_admin() ) {

	// Load sortable.js by Stuart Langridge
	function csv2sorttable_enqueue_scripts() {
		$mnsp_sorttable_js_url = plugins_url( '/scripts/sorttable.js', __FILE__ );
		wp_register_script( 'sorttable', $mnsp_sorttable_js_url );
		wp_enqueue_script( 'sorttable' );
	} 
	add_action( 'wp_enqueue_scripts', 'csv2sorttable_enqueue_scripts' ); 

	// Load default plugin styles
	function csv2sorttable_enqueue_styles() {
		$mnsp_style_url = plugins_url( '/css/csv2sorttable.css', __FILE__ );
		wp_register_style( 'csv2sorttable', $mnsp_style_url );
		wp_enqueue_style( 'csv2sorttable' );
	}
	add_action( 'wp_print_styles', 'csv2sorttable_enqueue_styles' );
}



// Shortcode to call the function below
add_shortcode( 'csv2table', 'csv2sorttable' );



// Import .csv file data
function csv2sorttable( $args ){
	
	
	/***********************************/
	/* INITIALIZE SHORTCODE PARAMETERS */
	/***********************************/
	
	
	// If no .csv file is defined using the 'source' shortcode parameter, display an error message
	if( !$args['source'] ) {
		echo '<div style="color: red;">Oops! You forgot to include the source of your .CSV file in the shortcode. Example: <strong>[csv2table source="http://yourdomain.com/yourfile.csv"]</strong></div>';
		return;
	}

	// Assign shortcode parameters to variables
	$opt_source =  $args['source']; // URL of the .csv file to import using the fopen() PHP function
	$opt_unsortable = explode( ',', $args['unsortable'] ); // Column numbers that should not be sortable
	$opt_numeric = explode( ',', $args['number'] ); // Column numbers that should be treated as numbers when sorting
	$opt_date = explode( ',', $args['date'] ); // Column numbers that should be treated as dates (mmdd) when sorting
	$opt_group = $args['group']; // Column to be used for grouping	
	if( $opt_group ) {
		$group = 0; // A common class will be assigned to adjacent rows with matching content
		$prev_cleancontent = ''; // Store content from the previous cell, to compare when grouping rows
		$evenodd = 'even';
	}
	global $opt_icons;
	$opt_icons = $args['icons']; // Enable file type icons if set
	
	
	/******************************************/
	/* IMPORT CONTENTS OF CSV FILE USING CURL */
	/******************************************/
	
	
	// Get .csv data using cURL method
	$session = curl_init();
	curl_setopt( $session, CURLOPT_RETURNTRANSFER, TRUE );
	curl_setopt( $session, CURLOPT_URL, $opt_source );
	$csv_data = curl_exec( $session ) or die( 'CURL ERROR: '.curl_error( $session ) );
	curl_close( $session );

	// Parse .csv data string into an indexed array
	$csv_data_array = mnsp_parse_csv( $csv_data );
	
	
	/********************************/
	/* BEGIN GENERATING HTML OUTPUT */
	/********************************/
	
	
	// Use output buffering
	ob_start();
	
	echo '<table class="sortable">';	
	
	// Begin ROW loop
	$row_num = 0;
	foreach( $csv_data_array as $row ) {
	
		// Define variables that will contain the HTML for each row
		if( $opt_group ) { $tr_start = '<tr'; } else { $tr_start = '<tr>'; }
		$tr_class = '';
		$tr_mid = '';
		$tr_end = '</tr>';
		
		//Begin COL loop
		$col_num = 1;
		foreach( $row as $cell ) {
			
			// Clean up the raw .csv content by converting special characters to HTML
			$cleancontent = htmlentities( $cell, ENT_QUOTES, 'ISO-8859-1' );
			$cleancontent = str_replace( 
				array( chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133) ),
				array( "'", "'", '"', '"', '-', '--', '...' ),
				$cleancontent
			);
			
			// Check .csv table data to see if the cell contains an email address, proper URL, or www address
			$cleancontent = mnsp_findlinks( $cleancontent );
			
			// Check if this is a groupe column
			if( $opt_group && $col_num == $opt_group ) { // If this column is set for grouping...
				if ( $row_num == 0 ) { // ...and it is the header row...
					$tr_class = '>'; // ...then just close the <tr> tag
				} else { // Otherwise, we are in the table body...
					if( !( $cleancontent == $prev_cleancontent ) ) { // ...so check the contents of the row above...
						$group++; // ...and if they don't match, begin the next group
						$prev_cleancontent = $cleancontent; // Then store the current cell contents as 'previous'
						if( $evenodd == 'even' ) { $evenodd = 'odd'; } else { $evenodd = 'even'; }
					}
				$tr_class = ' class="group' . $group . ' ' . $evenodd . '">'; // Assign the class 'groupX' to the <tr>
				}
			}
			
			
			// Create the <th> and <td> cells
			if( $row_num == 0 ) { // Header row <th>
				if( in_array( $col_num, $opt_unsortable ) ) { // This column is 'unsortable'
					$addclass = 'sorttable_nosort';
				} elseif( in_array( $col_num, $opt_numeric ) ) { // This column is set as 'number'
					$addclass = 'sorttable_numeric';
				} elseif( in_array( $col_num, $opt_date ) ) { // This column is set as 'date' (format: mmdd)
					$addclass = 'sorttable_mmdd';
				} else { // By default, data in columns is sortable alphabetically
					$addclass = 'sorttable_alpha';
				}
				$addclass .= ' col' . $col_num; // Each column gets a unique classe for styling column widths
				$tr_mid .= '<th class="' . $addclass . '">' . $cleancontent . '</th>';
			} else { // Cell is in body row <td>
				$tr_mid .= '<td class="col' . $col_num . '">' . $cleancontent . '</td>';
			}
			$col_num++;
			
		// End COL loop	
		}
		
		// Echo variables containing the HTML contents of the row
		echo $tr_start . $tr_class . $tr_mid . $tr_end;
		$row_num++;
		
	// End ROW loop
	}

	echo '</table>';

	// End of output buffering
	$content = ob_get_contents();;
	ob_end_clean();
	return $content;
}



// Function to parse .csv data into an indexed array
if ( !function_exists( 'mnsp_parse_csv' ) ) {
	function mnsp_parse_csv( $file, $comma=',', $quote='"', $newline="\n" ) { 
		$db_quote = $quote . $quote;
      
		// Clean up file 
		$file = trim( $file ); 
		$file = str_replace( "\r\n", $newline, $file ); 

		$file = str_replace( $db_quote, '"', $file ); // Replace double quote pairs with one double quote 
		$file = str_replace( ',",', ', ,', $file ); // Handle ,"", empty cells correctly 
		$file = str_replace( ',"\n', ',\n', $file ); // Handle ,""\n empty cells correctly at the end of lines 
		$file .= $comma; // Put a comma on the end, so we parse the last cell

		$inquotes = false; 
		$start_point = 0; 
		$row = 0; 
		$cellNo = 0; 

		for( $i=0; $i < strlen( $file ); $i++ ) { 

			$char = $file[$i];
				if( $char == $quote ) { 
					if( $inquotes ) { 
						$inquotes = false; 
					} else { 
						$inquotes = true; 
					}
				}

				if( ( $char == $comma or $char == $newline ) and !$inquotes ) { 
					$cell = substr( $file, $start_point, $i-$start_point ); 
					$cell = str_replace( $quote, '', $cell ); // Remove delimiter quotes 
					$cell = str_replace( '"', $quote, $cell ); // Add in data quotes 
					if ( $row > 0 ) $data[$row][$data[0][$cellNo]] = $cell; 
					else $data[$row][] = $cell; 
					$cellNo++; 
					$start_point = $i + 1; 
					if ( $char == $newline ) { 
						$cellNo = 0;
						$row ++;
					} 
				} 
		} 
		return $data; 
	}
}



// Find URLs and email addresses in .csv data and convert them to HTML links
if( !function_exists( 'mnsp_findlinks' ) ) {
	function mnsp_findlinks( $text ) {

		global $file_url;
		global $link_text;
		global $opt_icons;

		// Define regex patterns for email addresses, standard URLs, and WWW addresses
		define( 'MNSP_EMAIL_PATTERN', '/[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}/i' );
		define( 'MNSP_URL_PATTERN', '/((http|https|ftp|sftp):\/\/)[a-z0-9\-\._]+\/?[a-z0-9_\.\-\?\+\/~=&#;,]*[a-z0-9\/]{1}/si' );
		define( 'MNSP_WWW_PATTERN', '/(www)[a-z0-9\-\._]+\/?[a-z0-9_\.\-\?\+\/~=&#;,]*[a-z0-9\/]{1}/si' );

		// First, check if the string contains an email address...
		if( preg_match( MNSP_EMAIL_PATTERN, $text, $email ) ) {
			$replacement = '<a href="mailto:' . $email[0]. '">' . $email[0] . '</a> ';
			$text = preg_replace( MNSP_EMAIL_PATTERN, $replacement, $text );
		}

		// Next, check if the string contains a URL beginning with http://, https://, ftp://, or sftp://
		if( preg_match( MNSP_URL_PATTERN, $text, $url ) ) {
			$file_url = $url[0];
			if( $opt_icons ) { mnsp_fileicons( $file_url, $link_text ); } else { $link_text = $file_url; }
			$replacement = '<a href="' . $url[0]. '">' . $link_text . '</a> ';
			$text = preg_replace( MNSP_URL_PATTERN, $replacement, $text );

		// ...and if not, check for a plain old www address
		} elseif( preg_match( MNSP_WWW_PATTERN, $text, $www ) ) {
			$replacement = '<a href="http://' . $www[0]. '">' . $www[0] . '</a> ';
			$text = preg_replace( MNSP_WWW_PATTERN, $replacement, $text );
		}
 
	return $text; 
	}
}



// Convert link text to icon image for certain file types
if( !function_exists( 'mnsp_fileicons' ) ) {
	function mnsp_fileicons( $file_url, $link_text ) {

		global $file_url;
		global $link_text;

		$link_text = $file_url;
		
		// If the URL ends with a file extension that we have an icon for, change the link text to an img tag
		$file_url_ext = substr( $file_url, -3 ); // Get the last three characters of the url, to compare to available icon file extensions
		$valid_file_ext = array( 'doc', 'eps', 'gif', 'ind', 'jpg', 'mov', 'mp3', 'pdf', 'ppt', 'xls', 'zip' ); // We have icons for these file extensions
		foreach( $valid_file_ext as $extension ) {
			if( $extension == $file_url_ext ) {
				$icon = plugins_url( '/images/icon_' . $extension . '.png', __FILE__ );
				$link_text = '<img src="' . $icon . '" class="sortable_link_icon">';
				// Credit: File icon images courtesy of Blake Knight - http://blog.blake-knight.com/2010/06/15/free-vector-pack-document-icons/
				return $link_text;
			}
		}
		return;
	
	}
}

?>