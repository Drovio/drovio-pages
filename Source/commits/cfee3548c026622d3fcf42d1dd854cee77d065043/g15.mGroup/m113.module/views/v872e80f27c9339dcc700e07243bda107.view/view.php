<?php
//#section#[header]
// Module Declaration
$moduleID = 113;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\profiler\tester;
use \API\Developer\profiler\activityLogger;
use \API\Resources\archive\zipManager;
use \API\Resources\filesystem\directory;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\special\formCaptcha;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
 
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	// Check requirements
	$errorNtf = new formErrorNotification();
	$errorNtf->build();
	$hasError = FALSE;
	
	if (!formCaptcha::validate(simpleForm::getPostedFormID(), $_POST['releaseCaptcha']))
	{
		$hasError = TRUE;
		$header = $errorNtf->addErrorHeader("err", "CAPTCHA ERROR");
		$errorNtf->addErrorDescription($header, "errDesc", "Wrong Captcha code.", $extra = "");
	}
	
	if ($hasError)
		return $errorNtf->getReport();
	
	// Create Backup
	$suffix = "";
	$time = intval(date("G"));
	if ($time < 7)
		$time = "_night";
	else if ($time < 12)
		$time = "_start";
	else if ($time < 19)
		$time = "_noon";
	else
		$time = "_end";
	
	// example: 29jun2013_start
	$name = strtolower(date("dMY").$time);
	
	// Get ZipFile Name
	$trunkBackupName = systemRoot.tester::getTrunk()."/release/".$name.".zip";
	
	// Get Directory Contents
	$contents = directory::getContentList(systemRoot."/", FALSE);
	
	// Set time limit
	set_time_limit(100);
	
	// Backup
	zipManager::create($trunkBackupName, $contents, TRUE, TRUE);

	
	// Log activity
	$logDescription = "Site Release to ".$name.".zip at ".date("F j, Y, G:i (T)");
	activityLogger::log($logDescription);
	
	$status = TRUE;
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the page content
$pageContent = new HTMLContent();
$pageContent->build();


$title = moduleLiteral::get($moduleID, "lbl_releaseTitle");
$desc = DOM::create("h3", $title);
$pageContent->append($desc);

// Build Site Release form
$form = new simpleForm("siteRelease");
$publisherForm = $form->build($moduleID, "siteRelease")->get();
$pageContent->append($publisherForm);


$captcha = new formCaptcha();
$captchaElement = $captcha->build("siteRelease")->get();
$form->append($captchaElement);

$input = $form->getInput($type = "text", $name = "releaseCaptcha", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow("Captcha Code", $input, $required = TRUE, $notes = "");

// Return content report
return $pageContent->getReport();
//#section_end#
?>