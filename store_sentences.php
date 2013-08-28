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

// This script stores the sentences from the test file smo.txt in a db table.  By changing the target, you can also create an alternative sentences table for glossing, where multiwords are separated by an underscore instead of a space (run mark_multiwords.php first).

if (!isset($chain))
{
	include("includes/fns.php");
	include("/opt/gaidhlig/config.php");
}

$dir="./inputs";

//$files=scandir($dir);

// Uncomment for corpus.
// $sentences="sentences";
// $files=array("inputs/smo.txt");
// $end=".txt";

// Uncomment for multiwords.
//$sentences="sentences_mw";
$sentences="taic_mw";
//$files=array("inputs/smo.out");
$files=array("taic21-20.out");
$end=".out";

drop_existing_table($sentences);

$sql_table=query("
CREATE TABLE $sentences (
    id serial NOT NULL,
    filename character varying(100)  DEFAULT ''::character varying,
    surface text,
    english text,
    word_g integer,
    word_e integer
);
");

$sql_pkey=query("
ALTER TABLE ONLY ".$sentences." ADD CONSTRAINT ".$sentences."_pk PRIMARY KEY (id);
");

foreach ($files as $file)
{
	if (preg_match("/$end/", $file))  // Only act on .txt files.
	{
		$filename=basename(preg_replace("/\..*$/", "", $file));
		echo $filename;
		
		$lineno=0;
		
		$lines=file($dir."/".$file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $line)
		{
			$line=pg_escape_string(trim($line));
			
			if ($lineno%2==0)
			{
				$sql=query("insert into $sentences (filename, surface) values ('$filename', '$line');");
				echo $line."\n";

			}
			else
			{
				$sql=query("update $sentences set english='$line' where id=currval('{$sentences}_id_seq');");
				echo $line."\n\n";
			}
			
			$lineno++;
		}
	}
}

// Count up the words in each sentence.
exec("php add_wordcount.php");

?>