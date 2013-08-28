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

// This script builds a printable dictionary.

if (!isset($chain))
{
	include("includes/fns.php");
	include("/opt/gaidhlig/config.php");
}

$fp = fopen("outputs/dictionary.tex", "w") or die("Can't create the file");

$lines=file("tex/dict_header.tex");  // Open header file containing LaTeX markup to set up the document.
foreach ($lines as $line)
{
	fwrite($fp, $line);
}

$sql=query("select * from glalist order by surface;");
while ($row=pg_fetch_object($sql))
{
	$id=$row->id;
	$surface=$row->surface;
	$lemma=$row->lemma;
	$pos=$row->pos;
	$gender=$row->gender;
	$number=$row->number;
	$gcase=$row->gcase;
	$tense=$row->tense;
	$enlemma=$row->enlemma;
	$clar=$row->clar;
	$notes=$row->notes;
	
	// Headwords ...
	// For words that have a lemma pointing to another word, show the lemma ...
	if ( ($pos=='vn' or $pos=='v' or $pos=="prep+pron" or $pos=="prep+poss" or $pos=="prep" or $pos=="n") and $lemma!=$surface)
	{
		echo $surface." (<-- ".$lemma.") [".$pos." ".$gender." ".$number." ".$gcase." ".$tense."] ".$enlemma." ".$clar." ".$notes."\n";
		$headword="\\hw{".$surface."} ($\\leftarrow$ \\lw{".$lemma."}) \\textit{".$pos." ".$gender." ".$number." ".$gcase." ".$tense."} ".$enlemma." ".$clar." ".$notes."\n\n";
	}
	else  // ... otherwise don't
	{
		echo $surface." [".$pos." ".$gender." ".$number." ".$gcase." ".$tense."] ".$enlemma." ".$clar." ".$notes."\n";
		$headword="\\hw{".$surface."} \\textit{".$pos." ".$gender." ".$number." ".$gcase." ".$tense."} ".$enlemma." ".$clar." ".$notes."\n\n";
	}
	
	fwrite($fp, $headword);
	
	// Subwords ...
	// Stack words related by lemma under the lemma
	if ($pos=='v' and $tense=='imper')
	{
		$sql2=query("select * from glalist where lemma='$lemma' and id!=$id order by surface;");
		while ($row2=pg_fetch_object($sql2))
		{
			echo ">>> ".$row2->surface." [".$row2->pos." ".$row2->gender." ".$row2->number." ".$row2->gcase." ".$row2->tense."] ".$row2->enlemma." ".$row2->clar." ".$row2->notes."\n";
			fwrite($fp, "\\hspace{10mm} \\sw{".$row2->surface."} \\textit{".$row2->pos." ".$row2->gender." ".$row2->number." ".$row2->gcase." ".$row2->tense."} ".$row2->enlemma." ".$row2->clar." ".$row2->notes."\n\n");
		}
	}
	
	if ($pos=='prep')
	{
		$sql2=query("select * from glalist where lemma='$lemma' and id!=$id order by surface;");
		while ($row2=pg_fetch_object($sql2))
		{
			if ($row2->pos!='n')
			{
				echo ">>> ".$row2->surface." [".$row2->pos." ".$row2->gender." ".$row2->number." ".$row2->gcase." ".$row2->tense."] ".$row2->enlemma." ".$row2->clar." ".$row2->notes."\n";
				fwrite($fp, "\\hspace{10mm} \\sw{".$row2->surface."} \\textit{".$row2->pos." ".$row2->gender." ".$row2->number." ".$row2->gcase." ".$row2->tense."} ".$row2->enlemma." ".$row2->clar." ".$row2->notes."\n\n");
			}
		}
	}
	
	if ($pos=='n' and ($gcase=='' and $number!='pl') )
	{
		$sql2=query("select * from glalist where lemma='$lemma' and id!=$id order by surface;");
		while ($row2=pg_fetch_object($sql2))
		{
			if ($row2->pos!='v' and $row2->pos!='vn')
			{
				echo ">>> ".$row2->surface." [".$row2->pos." ".$row2->gender." ".$row2->number." ".$row2->gcase." ".$row2->tense."] ".$row2->enlemma." ".$row2->clar." ".$row2->notes."\n";
				fwrite($fp, "\\hspace{10mm} \\sw{".$row2->surface."} \\textit{".$row2->pos." ".$row2->gender." ".$row2->number." ".$row2->gcase." ".$row2->tense."} ".$row2->enlemma." ".$row2->clar." ".$row2->notes."\n\n");
			}
		}
	}
	
	unset($subword);

}

$lines=file("tex/tex_footer.tex");  // Open footer file.
foreach ($lines as $line)
{
	fwrite($fp, $line);
}

fclose($fp);

exec("xelatex -interaction=nonstopmode -output-directory=outputs dictionary.tex 2>&1");

?>