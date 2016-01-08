
##Gàidhlig autoglosser.##

###Introduction###

The autoglosser was originally developed for [Welsh](http://bangortalk.org.uk), so that bilingual conversational running text could be POS-tagged.  These tools were developed as a test of whether the autoglosser could be adapted for another Celtic language, and also created some baby resources for testing.

The test worked pretty well, but I ran into the bugbear of all minority language work -- hardly any resources are available under a free license! -- and life's too short to keep reinventing the wheel ...

Anyway, this is a basic howto for running the autoglosser for Gàidhlig. Contact me if you have problems.

The web interface and more information on the other resources is at [http://kevindonnelly.org.uk/gaidhlig](the main site).

###Required software###

The autoglosser was developed and tested on Ubuntu GNU/Linux.

PHP5, Apache2 and PostgreSQL should be installed.
The visl-cg3 constraint grammar implementation should be installed from [Tino Didriksen's PPA](https://launchpad.net/~tinodidriksen/+archive/cg3).
To get a typeset final output, LaTeX (TeXLive2012) should be installed, along with [John Frampton's expex package](http://www.ctan.org/pkg/expex).

###Setup and usage###

Import the Gàidhlig dictionary (`dbs/glalist.sql`) into a PostgreSQL table.

Move the gaidhlig directory into `/opt`, and adjust the connection details in `gaidhlig/config.php` to match your PostgreSQL setup.

The following assumes you have a bilingual corpus text you want to autogloss broken into aligned sentences and set out in the following format:

```
    Gàidhlig sentence
    English sentence
    blank line
```
If you have only Gàidhlig in the text, enter something like xxx for the English line. (Alternatively, you could change the import routine in `store_sentences.php` to handle a monolingual text.)

Place your corpus text in the `inputs` folder.

In `do_everything.php`, set the names for the tables containing the sentences of the corpus, the words of that corpus, the combined constraint grammar output, and the name of the corpus.

In `store_sentences.php` set the same names for the sentences and words tables, and give the filename of your input text.

In `store_words.php`, set the same names for the sentences and words tables.

In `mark_multiwords.php`, give the filename of your input text.

(The above is laborious, but it could obviously be streamlined in the future if more work is done on these tools.)

Then run the autoglosser pipeline: `php do_everything.php`.

###Parts of the pipeline###

In sequence, the following scripts are run:

`gather_multiwords.php` Collects multiwords from the dictionary and places them in a new table in order of the number of spaces they contain.

`mark_multiwords.php` Goes through the text and writes a new file in the outputs folder replacing the spaces in any multiwords it finds with an underscore.

`store_sentences.php` Imports each pair of lines from the text and stores them as parallel entries in the `sentences` table.

`store_words.php` Imports the Gàidhlig words of each sentence into a `words` table, removing the underscores added by `mark_multiwords.php`.

`write_cohorts.php` Takes each word and looks it up in the dictionary, writing out the results in the `outputs` folder in a format that visl-cg3 can consume.

`apply_cg.php` Applies constraint grammar disambiguation to the cohort file, writing out the disambiguated words in the `outputs` folder.  The grammar rules are in `grammar/gla_grammar`.

`write_cgfinished.php` Parses the CG output file and stores the results in a `cgfinished` table.

`join_tags.php` Gets the data for each Gàidhlig word and writes it into the `words` table.  If the disambiguation has worked well, there will only be one gloss, but in cases where there is more than one (which signals that more grammar rules need to be added to those in `grammar/gla_grammar`) the glosses will be combined into one string separated by `[or]`.

`generate_expex.php` Reads the sentences from the `sentences` table and the words+gloss from the `words` table, and writes out a tex file arranging them in parallel, with the words and glosses aligned.  Then runs LaTeX to create a pdf file in the outputs folder.

If you get UNK (unknown) as the gloss for a particular word, this means that the word needs to be added to the dictionary.
