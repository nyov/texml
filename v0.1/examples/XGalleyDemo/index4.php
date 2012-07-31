<?php
require(dirname(__FILE__) . '/php4_config.php');
require(dirname(__FILE__) . '/../../php/main.php');
$doc = esla_doc('mystyle');

$doc->text("Some starting text. Some starting text. Some starting text. Some starting text.");
$doc->text("Some starting text. Some starting text. Some starting text. Some starting text.");
$doc->text();
$doc->text("Some starting text. Some starting text. Some starting text. Some starting text.");
$doc->text("Some starting text. Some starting text. Some starting text. Some starting text.");

$doc->xstyle("parshape", "cutout", "no-page-break = true", "parshape-list = { 20 pt, 40 pt, 60 pt }");
$doc->style("section", "This should\\\\ be\\\\a really staggered\\\\heading (but isn't)");
$doc->text("In fact we should not be surprised since the standard \LaTeX{} heading");
$doc->text("code is essentially using its own parshape and thus overwriting the");
$doc->text("outer parshape declaration.");

$doc->xstyle("parshape", "cutout", "no-page-break = true", "parshape-list = { 20 pt, 40 pt, 60 pt }");
$doc->style("justification", "ragged-right");

$doc->text("This is a paragraph for testing various justification\\ settings. We have some");
$doc->text("text and after the word `word' \\ we just had a forced line break. We");
$doc->text("do some more line break testing, e.g., this\\[7pt] one was supposed to");
$doc->text("add $7$\,pt of extra space.");
$doc->text(); // empty row
$doc->text("And here a paragraph for comparison with some text some text some text");
$doc->text("also-containing-a-longer-word some text some text some text some text");
$doc->text("some text some text some text.");

$doc->style("section", "Linebreaks");
$doc->text("A line break with linebreak\linebreak");
$doc->text("and now \ldots");

$doc->style("section", "Justified");

$doc->text("some text");
$doc->style("marginpar", array( 
    esla_xstyle("justification", "ragged-right"),
    esla_style("fussy"),
    esla_style("fontfamily", "pop"),
    esla_style("fontsize", "7", "9"),
    esla_style("selectfont"),
    esla_text("ZZZ Some text in Optima. Some text in Optima. Some text in Optima."),
    esla_text("Some text in Optima. Some text in Optima."))
);

$doc->text("some text some text some text some text some text some text some text");
$doc->text("some text some text some text some text some text some text");
$doc->text("some text some text some text some text some text some text some text");
$doc->text("some text some text some text some text some text some text");
$doc->text("some text some text some text some text some text some text some text");
$doc->text("some text some text some text some text some text some text.");

$doc->style("section", "Ragged-right");

$doc->xstyle("justification", "ragged-right"); 

$doc->text("This is a paragraph for testing various justification settings. We have some");
$doc->text("text and after the word `word' \\ we just had a forced line break. We");
$doc->text("do some more line break testing, e.g., this\\[7pt] one was supposed to");
$doc->text("add $7$\,pt of extra space.");
$doc->text();
$doc->text("And here a paragraph for comparison with some text some text some text");
$doc->text("also-containing-a-longer-word some text some text some text some text");
$doc->text("some text some text some text.");

$doc->style("section", "Ragged-left");

$doc->xstyle("justification", "ragged-left");
$doc->xstyle("hyphenation", "std", "enable = false"); // make instance

$doc->style("sloppy");

$doc->text("This is a paragraph for testing various justification settings. We have some");
$doc->text("text and after the word `word' \\ we just had a forced line break. We");
$doc->text("do some more line break testing, e.g., this\\[7pt] one was supposed to");
$doc->text("add $7$\,pt of extra space.");
$doc->text();
$doc->text("And here a paragraph for comparison with some text some text some text");
$doc->text("also-containing-a-longer-word some text some text some text some text");
$doc->text("some text some text some text.");

$doc->style("section", "Adjust");

$xsname1 = 
$doc->xstyle("justification", "std", "end-skip = \\fill", "left-skip  = 0 pt", "right-skip = 0 pt", "start-skip = 0 pt")->iname();
$doc->xstyle("hyphenation", "std", "enable = true"); 
$doc->style("fussy");

$doc->text("This is a paragraph for testing various justification settings. We have some");
$doc->text("text and after the word `word' \\ we just had a forced line break. We");
$doc->text("do some more line break testing, e.g., this\\[7pt] one was supposed to");
$doc->text("add $7$\,pt of extra space.");
$doc->text();
$doc->text("And here a paragraph for comparison with some text some text some text");
$doc->text("also-containing-a-longer-word some text some text some text some text");
$doc->text("some text some text some text.");

$doc->style("section", "Centered");

$doc->xstyle("justification", "centered");

$doc->text("This is a paragraph for testing various justification settings. We have some");
$doc->text("text and after the word `word' \\ we just had a forced line break. We");
$doc->text("do some more line break testing, e.g., this\\[7pt] one was supposed to");
$doc->text("add $7$\,pt of extra space.");
$doc->text();
$doc->text("And here a paragraph for comparison with some text some text some text");
$doc->text("also-containing-a-longer-word some text some text some text some text");
$doc->text("some text some text some text.");

$doc->style("section", "Center first");
$doc->xstyle("justification", "compound", "first-paragraph  = centerfirst", "other-paragraphs = ". $xsname1);

$doc->text("This is a paragraph for testing various justification settings. We have some");
$doc->text("This is a paragraph for testing various justification settings. We have some");
$doc->text("This is a paragraph for testing various justification settings. We have some");
$doc->text("This is a paragraph for testing various justification settings. We have some");
$doc->text("text and after the word `word' \\ we just had a forced line break. We");
$doc->text("do some more line break testing, e.g., this\\[7pt] one was supposed to");
$doc->text("add $7$\,pt of extra space.");
$doc->text("This is a paragraph for testing various justification settings. We have some");
$doc->text("This is a paragraph for testing various justification settings. We have some");
$doc->text("This is a paragraph for testing various justification settings. We have some");
$doc->text("This is a paragraph for testing various justification settings. We have some");
$doc->text();
$doc->text("And here a paragraph for comparison with some text some text some text");
$doc->text("also-containing-a-longer-word some text some text some text some text");
$doc->text("some text some text some text.");

$doc->style("section", "Centering");

$doc->xstyle("justification", "centering");

$doc->text("This is a paragraph for testing various justification settings. We have some");
$doc->text("text and after the word `word' \\ we just had a forced line break. We");
$doc->text("do some more line break testing, e.g., this\\[7pt] one was supposed to");
$doc->text("add $7$\,pt of extra space.");
$doc->text();
$doc->text("And here a paragraph for comparison with some text some text some text");
$doc->text("also-containing-a-longer-word some text some text some text some text");
$doc->text("some text some text some text.");

esla_pdf($doc, dirname(__FILE__) . '/test2.pdf', dirname(__FILE__) . '/styles');
?>
