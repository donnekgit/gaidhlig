<?php

/* 
*********************************************************************
Copyright Kevin Donnelly 2010, 2011.
kevindonnelly.org.uk
This file is part of the Bangor Autoglosser.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License and the GNU
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

// This file handles dictionary lookups in the Gàidhlig dictionary, glalist.  First, the surface word is demutated, and then each of these is looked up, but only where the demutated word is actually different from the surface word.  Note that demutated words need to be looked up separately since in a few cases there are homophonous pairs, one with a mutation and one without.

// Uncomment for testing.
// include("includes/fns.php");
// include("/opt/gaidhlig/config.php");
// $surface="th'";
// echo $surface."\n";
// $surface=pg_escape_string($surface);  // Required to allow lookup of words containing an apostrophe.

$orig_surface=$surface;  // Store as a test condition.

$deasp_surface=de_asp($surface);  // Deaspirate the word, eg Ghàidhlig -> Gàidhlig, chathraiche -> cathraiche.
//echo $deasp_surface."\n";
$depre_surface=de_pre($surface);  // Deprefix the word, eg h-uile -> uile, dh'fheumas -> feumas.
//echo $depre_surface."\n";
$desuf_surface=de_suf($surface);  // Desuffix the lowercased word.
//echo $desuf_surface."\n";

$lowsurface=mb_strtolower($surface, "UTF-8");  // Lowercase the word (need to use this function to handle accented capital letters).
//echo $surface."\n";

$deasp_lowsurface=de_asp($lowsurface);  // Deaspirate the lowercased word, eg Chathraiche -> Cathraiche (ie ordinary words that are capitalised).
//echo $deasp_lowsurface."\n";
$depre_lowsurface=de_pre($lowsurface);  // Deprefix the lowercased word, eg t-Sabhal -> sabhal (ie ordinary words that are capitalised).
//echo $depre_lowsurface."\n";

$desuf_lowsurface=de_suf($lowsurface);  // Desuffix the lowercased word.
//echo $desuf_lowsurface."\n";

$dictlist=array();

// The code repetitions below could be minimised by looping through the query, inserting a different search form each time. 

$sql_surf=query("select * from glalist where surface='$surface';");
while ($row_surf=pg_fetch_object($sql_surf))
{   
    array_push($dictlist, $row_surf->id);
    $lemma="\t\"".$row_surf->lemma."\" ";
    $pos=$row_surf->pos." ";
    $gender=($row_surf->gender =='') ? "" : $row_surf->gender." ";
    $number=($row_surf->number =='') ? "" : $row_surf->number." ";
    $gcase=($row_surf->gcase =='') ? "" : $row_surf->gcase." ";
    $tense=($row_surf->tense =='') ? "" : $row_surf->tense." ";
    $notes=($row_surf->notes =='') ? "" : $row_surf->notes." ";
    $enlemma=":".$row_surf->enlemma.": ";
    $id="[".$row_surf->id."]";
    $entry=pg_escape_string($lemma.$place."[gla] ".$pos.$gender.$number.$gcase.$tense.$enlemma.$id)."\n"; 
    fwrite($fp, $entry);  // Write
    echo $entry;  // View
    unset($entry);  // Clear the decks
}

if ($lowsurface!=$surface)
{
	$sql_low=query("select * from glalist where surface='$lowsurface';");
	while ($row_low=pg_fetch_object($sql_low))
	{   
		array_push($dictlist, $row_low->id);
		$lemma="\t\"".$row_low->lemma."\" ";
		$pos=$row_low->pos." ";
		$gender=($row_low->gender =='') ? "" : $row_low->gender." ";
		$number=($row_low->number =='') ? "" : $row_low->number." ";
		$gcase=($row_low->gcase =='') ? "" : $row_low->gcase." ";
		$tense=($row_low->tense =='') ? "" : $row_low->tense." ";
		$notes=($row_low->notes =='') ? "" : $row_low->notes." ";
		$enlemma=":".$row_low->enlemma.": ";
		$id="[".$row_low->id."]";
		$entry=pg_escape_string($lemma.$place."[gla] ".$pos.$gender.$number.$gcase.$tense.$enlemma.$id)."\n"; 
		fwrite($fp, $entry);  // Write
		echo $entry;  // View
		unset($entry);  // Clear the decks
	}
}

if ($deasp_surface!=$surface)
{
    $sql_asp=query("select * from glalist where surface='$deasp_surface';");
    while ($row_asp=pg_fetch_object($sql_asp))
    {
        if (!in_array($row_asp->id, $dictlist))
        {
            array_push($dictlist, $row_asp->id);
            $lemma="\t\"".$row_asp->lemma."\" ";
            $pos=$row_asp->pos." ";
            $gender=($row_asp->gender =='') ? "" : $row_asp->gender." ";
            $number=($row_asp->number =='') ? "" : $row_asp->number." ";
            $gcase=($row_asp->gcase =='') ? "" : $row_asp->gcase." ";   
            $tense=($row_asp->tense =='') ? "" : $row_asp->tense." ";
            $notes=($row_asp->notes =='') ? "" : $row_asp->notes." ";
            $enlemma=":".$row_asp->enlemma.": ";
            $id="[".$row_asp->id."]";
            $entry=pg_escape_string($lemma.$place."[gla] ".$pos.$gender.$number.$gcase.$tense.$enlemma.$id." + asp")."\n"; 
            fwrite($fp, $entry);  // Write
            echo $entry;  // View
            unset($entry);  // Clear the decks
        }
    }
}

if ($deasp_lowsurface!=$lowsurface)
{
    $sql_asplow=query("select * from glalist where surface='$deasp_lowsurface';");
    while ($row_asplow=pg_fetch_object($sql_asplow))
    {
        if (!in_array($row_asplow->id, $dictlist))
        {
            array_push($dictlist, $row_asplow->id);
            $lemma="\t\"".$row_asplow->lemma."\" ";
            $pos=$row_asplow->pos." ";
            $gender=($row_asplow->gender =='') ? "" : $row_asplow->gender." ";
            $number=($row_asplow->number =='') ? "" : $row_asplow->number." ";
            $gcase=($row_asplow->gcase =='') ? "" : $row_asplow->gcase." ";
            $tense=($row_asplow->tense =='') ? "" : $row_asplow->tense." ";
            $notes=($row_asplow->notes =='') ? "" : $row_asplow->notes." ";
            $enlemma=":".$row_asplow->enlemma.": ";
            $id="[".$row_asplow->id."]";
            $entry=pg_escape_string($lemma.$place."[gla] ".$pos.$gender.$number.$gcase.$tense.$enlemma.$id." + asp")."\n"; 
            fwrite($fp, $entry);  // Write
            echo $entry;  // View
            unset($entry);  // Clear the decks
        }
    }
}

if ($depre_surface!=$surface)
{
	if (preg_match("/^dh/", $surface))
	{
		$pre=" + dh";
	}
	elseif (preg_match("/^d[^h]/", $surface))
	{
		$pre=" + d";
	}
	elseif (preg_match("/^h-/", $surface))
	{
		$pre=" + h";
	}
	elseif (preg_match("/^n-/", $surface))
	{
		$pre=" + n";
	}
	elseif (preg_match("/^t-/", $surface))
	{
		$pre=" + t";
	}	

    $sql_pre=query("select * from glalist where surface='$depre_surface';");
    while ($row_pre=pg_fetch_object($sql_pre))
    {
        if (!in_array($row_pre->id, $dictlist))
        {
            array_push($dictlist, $row_pre->id);
            $lemma="\t\"".$row_pre->lemma."\" ";
            $pos=$row_pre->pos." ";
            $gender=($row_pre->gender =='') ? "" : $row_pre->gender." ";
            $number=($row_pre->number =='') ? "" : $row_pre->number." ";
            $gcase=($row_pre->gcase =='') ? "" : $row_pre->gcase." ";
            $tense=($row_pre->tense =='') ? "" : $row_pre->tense." ";
            $notes=($row_pre->notes =='') ? "" : $row_pre->notes." ";
            $enlemma=":".$row_pre->enlemma.": ";
            $id="[".$row_pre->id."]";
            $entry=pg_escape_string($lemma.$place."[gla] ".$pos.$gender.$number.$gcase.$tense.$enlemma.$id.$pre)."\n"; 
            fwrite($fp, $entry);  // Write
            echo $entry;  // View
            unset($entry);  // Clear the decks
        }
    }
}

if ($desuf_lowsurface!=$lowsurface)
{
	if (preg_match("/-s(e|an?)/", $lowsurface))
	{
		$suf=" + emph";
	}

    $sql_suflow=query("select * from glalist where surface='$desuf_lowsurface';");
    while ($row_suflow=pg_fetch_object($sql_suflow))
    {
        if (!in_array($row_suflow->id, $dictlist))
        {
            array_push($dictlist, $row_suflow->id);
            $lemma="\t\"".$row_suflow->lemma."\" ";
            $pos=$row_suflow->pos." ";
            $gender=($row_suflow->gender =='') ? "" : $row_suflow->gender." ";
            $number=($row_suflow->number =='') ? "" : $row_suflow->number." ";
            $gcase=($row_suflow->gcase =='') ? "" : $row_suflow->gcase." ";
            $tense=($row_suflow->tense =='') ? "" : $row_suflow->tense." ";
            $notes=($row_suflow->notes =='') ? "" : $row_suflow->notes." ";
            $enlemma=":".$row_suflow->enlemma.": ";
            $id="[".$row_suflow->id."]";
            $entry=pg_escape_string($lemma.$place."[gla] ".$pos.$gender.$number.$gcase.$tense.$enlemma.$id.$suf)."\n"; 
            fwrite($fp, $entry);  // Write
            echo $entry;  // View
            unset($entry);  // Clear the decks
        }
    }
}

if ($desuf_surface!=$surface)
{
	if (preg_match("/-s(e|an?)/", $lowsurface))
	{
		$suf=" + emph";
	}

    $sql_suf=query("select * from glalist where surface='$desuf_surface';");
    while ($row_suf=pg_fetch_object($sql_suf))
    {
        if (!in_array($row_suf->id, $dictlist))
        {
            array_push($dictlist, $row_suf->id);
            $lemma="\t\"".$row_suf->lemma."\" ";
            $pos=$row_suf->pos." ";
            $gender=($row_suf->gender =='') ? "" : $row_suf->gender." ";
            $number=($row_suf->number =='') ? "" : $row_suf->number." ";
            $gcase=($row_suf->gcase =='') ? "" : $row_suf->gcase." ";
            $tense=($row_suf->tense =='') ? "" : $row_suf->tense." ";
            $notes=($row_suf->notes =='') ? "" : $row_suf->notes." ";
            $enlemma=":".$row_suf->enlemma.": ";
            $id="[".$row_suf->id."]";
            $entry=pg_escape_string($lemma.$place."[gla] ".$pos.$gender.$number.$gcase.$tense.$enlemma.$id.$suf)."\n"; 
            fwrite($fp, $entry);  // Write
            echo $entry;  // View
            unset($entry);  // Clear the decks
        }
    }
}

if ($depre_lowsurface!=$lowsurface)
{
	if (preg_match("/^[Dd]h'/", $lowsurface))
	{
		$pre=" + dh";
		$depre_lowsurface=de_asp(preg_replace("/[Dd]h'/", "", $depre_lowsurface));
	}
	elseif (preg_match("/^d[^h]/", $surface))
	{
		$pre=" + d";
	}
	elseif (preg_match("/^h-/", $lowsurface))
	{
		$pre=" + h";
	}
	elseif (preg_match("/^n-/", $lowsurface))
	{
		$pre=" + n";
	}
	elseif (preg_match("/^t-/", $lowsurface))
	{
		$pre=" + t";
	}	

    $sql_prelow=query("select * from glalist where surface='$depre_lowsurface';");
    while ($row_prelow=pg_fetch_object($sql_prelow))
    {
        if (!in_array($row_prelow->id, $dictlist))
        {
            array_push($dictlist, $row_prelow->id);
            $lemma="\t\"".$row_prelow->lemma."\" ";
            $pos=$row_prelow->pos." ";
            $gender=($row_prelow->gender =='') ? "" : $row_prelow->gender." ";
            $number=($row_prelow->number =='') ? "" : $row_prelow->number." ";
            $gcase=($row_prelow->gcase =='') ? "" : $row_prelow->gcase." ";
            $tense=($row_prelow->tense =='') ? "" : $row_prelow->tense." ";
            $notes=($row_prelow->notes =='') ? "" : $row_prelow->notes." ";
            $enlemma=":".$row_prelow->enlemma.": ";
            $id="[".$row_prelow->id."]";
            $entry=pg_escape_string($lemma.$place."[gla] ".$pos.$gender.$number.$gcase.$tense.$enlemma.$id.$pre)."\n"; 
            fwrite($fp, $entry);  // Write
            echo $entry;  // View
            unset($entry);  // Clear the decks
        }
    }
}

if (count($dictlist)<1)  // If we haven't found anything in the dictionary after the lookups of the various forms above ...
{
	if (preg_match("/^[A-ZÀÈÌÒÙ][a-zàèìòù]+$/u", $orig_surface))
	{
		$tag="name";
	}
	elseif (preg_match("/^[0-9]+(\.[0-9]+)?%$/", $orig_surface))
	{
		$tag="percent";
	}
	elseif (preg_match("/^[0-9]{4}$/", $orig_surface))
	{
		$tag="year";
	}
	elseif (preg_match("/^[0-9]+$/", $orig_surface))
	{
		$tag="diginum";
	}
	else
	{
		$tag="unk";
	}

	$entry="\t\"".$surface."\" ".$place."[und] ".$tag." :".$surface.": [0]\n";
	echo $entry;  // View
	fwrite($fp, $entry);  // Write
	unset($entry);  // Clear the decks
}

?>
