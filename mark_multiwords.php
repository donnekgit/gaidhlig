<?php

/* 
*********************************************************************
Copyright Kevin Donnelly 2013.
kevindonnelly.org.uk
This file is part of the Bangor Autoglosser for Gàidhlig.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License or the GNU
Affero General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option)
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
and the GNU Affero General Public License along with this program.
If not, see <http://www.gnu.org/licenses/>.
*********************************************************************
*/

// This script creates a copy of the input file with multiwords connected with an underscore.  Run gather_multiwords.php first.

if (!isset($chain))
{
	include("includes/fns.php");
	include("/opt/gaidhlig/config.php");
}

$multiwords="multiwords";

//$files=scandir($dir);
//$infiles=array("smo.txt");
$infiles=array("inputs/taic1-10.txt");

foreach ($infiles as $infile)
{
	$outfile=preg_replace("/\.txt$/", ".out", $infile);
	
	// Copy the file.
	$output=file_get_contents($infile);
	// 		echo $output."\n";
	
	$sql=query("select surface from $multiwords order by id;");
	while ($row=pg_fetch_object($sql))
	{
		// Non-capitalised forms.
		$surface=$row->surface;
		$replace=preg_replace("/ /", "_", $row->surface);
		echo $replace."\n";
		$output=preg_replace("/$surface/", "$replace", $output);
		
		// Capitalised forms.
		$surface=ucfirst($surface);
		$replace=ucfirst($replace);
		//echo $replace."\n";
		$output=preg_replace("/$surface/", "$replace", $output);
	}
	
	file_put_contents($outfile, $output);
}

?>