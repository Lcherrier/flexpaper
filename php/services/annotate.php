<?php
/**
* █▒▓▒░ The FlexPaper Project
*
* Copyright (c) 2009 - 2011 Devaldi Ltd
*
* SWF text extraction for PHP. Executes the specified text extraction
* executable and returns the output
*
* GNU GENERAL PUBLIC LICENSE Version 3 (GPL).
*
* The GPL requires that you not remove the FlexPaper copyright notices
* from the user interface.
*
* Commercial licenses are available. The commercial player version
* does not require any FlexPaper notices or texts and also provides
* some additional features.
* When purchasing a commercial license, its terms substitute this license.
* Please see http://flexpaper.devaldi.com/ for further details.
*
*/
require_once("../lib/common.php");
require_once("../lib/annotatepdf_php5.php");

$doc            = $_POST["doc"];
$stamp          = $_POST["stamp"];

if(endsWith($doc,'.pdf')){
    $doc = substr($doc,strlen($doc)-4);
}

if(isset($_GET["subfolder"])){$subfolder=$_POST["subfolder"];}else{$subfolder="";}

$annotator      = new pdfannotator();
$messages       = $annotator->annotate($doc,$subfolder,$stamp);
$configManager 	= new Config();

if(is_numeric(strpos($messages,"[OK"))){
    // Read the annotated pdf and send it to the browser. Then delete the annotated file.
    $identifier = substr($messages,5,strrpos($messages,"'")-5);

    header('Content-type: application/pdf');

    echo "services/view.php?doc=" . $doc . "_annotated" . $identifier . ".pdf" . "&format=pdf&marked=true" . "&subfolder=" . $subfolder;
}else{
    echo $messages;
}
?>
