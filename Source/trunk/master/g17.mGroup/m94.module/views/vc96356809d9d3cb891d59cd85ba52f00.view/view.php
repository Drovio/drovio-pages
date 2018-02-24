<?php
//#section#[header]
// Module Declaration
$moduleID = 94;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");
importer::import("DEV", "Profiler", "logger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Resources\url;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;

$mc = new MContent();
$actionFactory = $mc->getActionFactory();

if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Library Name
	$empty = (is_null($_POST['wName']) || empty($_POST['wName']));
	if ($empty)
	{
		$has_error = TRUE;
		
		// Header
		$err_header = "Website Name";
		$err = $errFormNtf->addErrorHeader("web_h", $err_header);
		$errFormNtf->addErrorDescription($err, "web_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Create website
	$result = TRUE;
	$websiteID = 1;

	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = "Website";
		$err = $errFormNtf->addErrorHeader("web_h", $err_header);
		$errFormNtf->addErrorDescription($err, "web_desc", DOM::create("span", "Error creating website..."));
		return $errFormNtf->getReport();
	}
	
	// Redirect to designer
	$url = url::resolve("web", "/websites/website.php");
	$params = array();
	$params['id'] = $websiteID;
	$url = url::get($url, $params);
	return $actionFactory->getReportRedirect($url, $domain = "", $formSubmit = TRUE);
}
//#section_end#
?>