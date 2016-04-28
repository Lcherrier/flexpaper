<?php
/**
* █▒▓▒░ The FlexPaper Project
*
* Copyright (c) 2009 - 2011 Devaldi Ltd
*
* When purchasing a commercial license, its terms substitute this license.
* Please see http://flexpaper.devaldi.com/ for further details.
*
*/

require_once("config.php");
require_once("common.php");

class pdfannotator
{
	private $configManager = null;

	/**
	* Constructor
	*/
	function __construct()
	{
		$this->configManager = new Config();
	}

	/**
	* Destructor
	*/
	function __destruct() {

    }

	/**
	* Method:annotate
	*/
	public function annotate($doc,$subfolder,$stampfile)
	{
	    if(!endsWith($doc,'.pdf')){$pdfdoc 	= $doc . ".pdf";}else{$pdfdoc 	= $doc;}
	    $pdfFilePath 	= $this->configManager->getConfig('path.pdf') . $subfolder . $pdfdoc;

        if(!validPdfParams($pdfFilePath,$pdfdoc,null)){
            return "Error:Incorrect file specified, please check your path";
        }

	    // save stampfile to docs directory
	    $random         = rand(5, 10000);
	    $stampfilename  = $doc . "_stampfile" . $random . ".pdf";

        $ifp = fopen($this->configManager->getConfig('path.swf') . $stampfilename, "wb");
        $data = explode(',', $stampfile);

        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);

		$output=array();
        $command = $this->configManager->getConfig('cmd.conversion.multistamppdf');
        $command = str_replace("{path.pdf}",$this->configManager->getConfig('path.pdf') . $subfolder,$command);
        $command = str_replace("{path.swf}",$this->configManager->getConfig('path.swf') . $subfolder,$command);
        $command = str_replace("{stampfile}",$stampfilename,$command);
        $command = str_replace("{random}",$random,$command);
        $command = str_replace("{pdffile}",$pdfdoc,$command);
        $command = str_replace("{annotatedfile}",$doc . "_annotated" . $random . ".pdf" ,$command);

		try {
    		$return_var=0;
            exec($command,$output,$return_var);

            $errmsgs = trim(arrayToString($output));

            if($return_var==1 || $return_var==0 || (strstr(PHP_OS, "WIN") && $return_var==1)){
                unlink($this->configManager->getConfig('path.swf') . $stampfilename);
                return "[OK,'" . $random . "']";
            }else{
                return "[Error annotating PDF, please check your configuration]";
            }
		} catch (Exception $ex) {
			return $ex;
		}
	}
}
?>