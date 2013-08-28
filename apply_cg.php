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

if (!isset($chain))
{
	include("includes/fns.php");
	include("/opt/gaidhlig/config.php");
	$words="words";
}

$filename="outputs/{$words}_cg";
$gram_file="gla";

// Create wayback files if they don't exist.
exec("touch ".$filename."_applied_old.txt");
exec("touch ".$filename."_applied_old_old.txt");
// Copy cg_applied files one step backwards.
exec("mv ".$filename."_applied_old.txt ".$filename."_applied_old_old.txt"); 
exec("mv ".$filename."_applied.txt ".$filename."_applied_old.txt"); 

$fp = fopen("{$filename}_applied.txt", "w") or die("Can't create the file");

// To run a trace, use this line instead:
//exec("vislcg3 -g grammar/".$gram_file."_grammar --trace -I words_cg.txt", $cg_output);
exec("vislcg3 -g grammar/".$gram_file."_grammar -I ".$filename.".txt", $cg_output);
foreach ($cg_output as $cg_line)
{
	echo $cg_line."\n";
	fwrite($fp, $cg_line."\n");
}

fclose($fp);

?>
