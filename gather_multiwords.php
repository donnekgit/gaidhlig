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

// This script copies all the multiwords from glalist to a separate table, so that they can be used to mark up the additional copy of the input file that will form the sentences table that produces the words table.

if (!isset($chain))
{
	include("includes/fns.php");
	include("/opt/gaidhlig/config.php");
}

$multiwords="multiwords";

drop_existing_table($multiwords);

$sql_table=query("
CREATE TABLE multiwords (
	id serial NOT NULL,
    glaid integer,
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

$sql_pkey=query("
ALTER TABLE ONLY ".$multiwords." ADD CONSTRAINT ".$multiwords."_pk PRIMARY KEY (id);
");

// The multiwords with the most number of spaces need to come first, to prevent shorter phrases with some of the same words firing.
// These queries could probably be generated using a loop.
$sql4=query("insert into multiwords (glaid, surface, lemma, enlemma, clar, pos, gender, number, gcase, tense, notes, extra) select id, surface, lemma, enlemma, clar, pos, gender, number, gcase, tense, notes, extra from glalist where surface~'^.[^\\\s]*\\\s.[^\\\s]*\\\s.[^\\\s]*\\\s.[^\\\s]*\\\s.[^\\\s]*$' order by surface;");
$sql3=query("insert into multiwords (glaid, surface, lemma, enlemma, clar, pos, gender, number, gcase, tense, notes, extra) select id, surface, lemma, enlemma, clar, pos, gender, number, gcase, tense, notes, extra from glalist where surface~'^.[^\\\s]*\\\s.[^\\\s]*\\\s.[^\\\s]*\\\s.[^\\\s]*$' order by surface;");
$sql2=query("insert into multiwords (glaid, surface, lemma, enlemma, clar, pos, gender, number, gcase, tense, notes, extra) select id, surface, lemma, enlemma, clar, pos, gender, number, gcase, tense, notes, extra from glalist where surface~'^.[^\\\s]*\\\s.[^\\\s]*\\\s.[^\\\s]*$' order by surface;");
$sql1=query("insert into multiwords (glaid, surface, lemma, enlemma, clar, pos, gender, number, gcase, tense, notes, extra) select id, surface, lemma, enlemma, clar, pos, gender, number, gcase, tense, notes, extra from glalist where surface~'^.[^\\\s]*\\\s.[^\\\s]*$' order by surface;");

?>