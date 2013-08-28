<?php

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