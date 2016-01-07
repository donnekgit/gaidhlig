<?php

// This script sets up an empty table in the glalist format.  Edit the query at the end to populate the new table.

include("includes/fns.php");
include("/opt/gaidhlig/config.php");
$newtable="taic_unk";

drop_existing_table($newtable);

$sql_table = query("
CREATE TABLE $newtable (
    {$newtable}_id serial NOT NULL,
    surface character varying(100) DEFAULT ''::character varying,
    lemma character varying(100) DEFAULT ''::character varying,
    enlemma character varying(100) DEFAULT ''::character varying,
    clar character varying(100) DEFAULT ''::character varying,
    pos character varying(20) DEFAULT ''::character varying,
    gender character varying(20) DEFAULT ''::character varying,
    number character varying(50) DEFAULT ''::character varying,
    gcase character varying(20) DEFAULT ''::character varying,
    tense character varying(100) DEFAULT ''::character varying,
    notes character varying(250) DEFAULT ''::character varying,
    extra character varying(100) DEFAULT ''::character varying
);
");

$sql_pkey=query("alter table only $newtable add constraint {$newtable}_id_pkey primary key ({$newtable}_id);");

$sql=query("select surface, auto, count(surface) from taic_words where auto~'\\.UNK' group by surface, auto order by surface;");
while ($row=pg_fetch_object($sql))
{
	$surface=pg_escape_string(trim($row->surface));
	$sql_g=query("insert into $newtable (surface) values ('$surface');");
}

?>
