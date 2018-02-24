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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Html");
importer::import("DEV", "Tools");
//#section_end#
//#section#[code]
use \DEV\Tools\dbSync;
use \API\Literals\moduleLiteral;
use \API\Profile\person;
use \API\Security\account;
use \UI\Html\HTMLContent;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;

if ($_SERVER['REQUEST_METHOD'] == "POST")
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
	
	// If error, show notification
	if ($hasError)
		return $errorNtf->getReport();
	
	// Sync database information
	$result = dbSync::sync($_POST['sync']);
	
	
	// If there is an error in creating the library, show it
	if ($result !== TRUE)
	{
		$err = $errorNtf->addErrorHeader("libName_h", "Database Data Synchronization");
		$errorText = DOM::create("span", "Database schemas don't match! Please sync schemas and try again.");
		$errorNtf->addErrorDescription($err, "libName_desc", $errorText);
		return $errorNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

$form = new simpleForm();

// Build the page content
$pageContent = new HTMLContent();
$pageContent->build("error");
return $form->getSubmitContent($pageContent->get());
//#section_end#
?>