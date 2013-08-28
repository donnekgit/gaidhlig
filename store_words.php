<?php

// This script splits the sentences from store_sentences.php into words and stores them in a db table.

if (!isset($chain))
{
	include("includes/fns.php");
	include("/opt/gaidhlig/config.php");
}

//$sentences="sentences";
//$sentences="sentences_mw";
$sentences="taic_mw";
$words="taic_words";

$thesewords=array();

drop_existing_table($words);

$sql_table = query("
CREATE TABLE $words (
    {$words}_id serial NOT NULL,
    filename character varying(100),
    sentence_id integer,
    location integer,
    surface character varying(100),
    auto character varying(250)
);
");

$sql_pkey = query("
ALTER TABLE ONLY ".$words." ADD CONSTRAINT ".$words."_pk PRIMARY KEY ({$words}_id);
");

$sql=query("select * from $sentences order by id;");
while ($row=pg_fetch_object($sql))
{
	$newutt=trim($row->surface);

    $i=1; 
    
	$surface_words=array_filter(explode(' ', $newutt));

    foreach ($surface_words as $surface_word)
    {
		echo $row->id.": ".$surface_word."\n";
        $surface_word=pg_escape_string(trim($surface_word));
		$clean_word=wordclean($surface_word);
		$clean_word=preg_replace("/_/", " ", $clean_word);
		$sql_w=query("insert into $words (sentence_id, location, surface, filename) values ('$row->id', '$i', '$clean_word', '$row->filename')");
		$i=++$i;
	} 
	
	unset($newutt);
}

?>