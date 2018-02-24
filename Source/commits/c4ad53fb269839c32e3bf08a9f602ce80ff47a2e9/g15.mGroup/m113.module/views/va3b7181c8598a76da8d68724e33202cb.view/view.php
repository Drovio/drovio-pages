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
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Profile\person;
use \API\Profile\tester;
use \API\Resources\archive\zipManager;
use \API\Resources\filesystem\directory;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Security\account;
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
	
	// Authenticate account
	$username = person::getUsername();
	$password = $_POST['password'];
	if (!account::authenticate($username, $password))
	{
		$hasError = TRUE;
		$hd = moduleLiteral::get($moduleID, "authentication_error_header");
		$header = $errorNtf->addErrorHeader("err", $hd);
		$desc = moduleLiteral::get($moduleID, "authentication_error_msg");
		$errorNtf->addErrorDescription($header, "errDesc", $desc, $extra = "");
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
	$trunkBackupName = systemRoot.tester::getTrunk()."/backup/".$name.".zip";
	
	// Get Directory Contents
	$contents = directory::getContentList(systemRoot."/", TRUE);
	
	// Set time limit
	set_time_limit(600);
	
	// Backup
	zipManager::create($trunkBackupName, $contents, TRUE, TRUE);
	
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


$title = moduleLiteral::get($moduleID, "lbl_backupTitle");
$desc = DOM::create("h3", $title);
$pageContent->append($desc);

// Build backup's form
$form = new simpleForm("backup");
$publisherForm = $form->build($moduleID, "siteBackup")->get();
$pageContent->append($publisherForm);


// Header
$literal = moduleLiteral::get($moduleID, "lbl_authenticate");
$hd = DOM::create("h4", $literal);
$form->append($hd);

$title = literal::dictionary("password");
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Return content report
return $pageContent->getReport();
//#section_end#
?>