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

include("includes/fns.php");
include("/opt/gaidhlig/config.php");
//$utterances="taic_mw";
$sentences="taic1_mw";
$words="taic1_words";
$cgfinished="taic1_cgfinished";
$corpus="taic1";

$chain=1;

include("gather_multiwords.php");

include("mark_multiwords.php");

include("store_sentences.php");  //Remember to change the target so that it uses the multiword-marked file.

include("store_words.php");

include("write_cohorts.php");

include("apply_cg.php");

include("write_cgfinished.php");

include("join_tags.php");

include("generate_expex.php");

?>
