<?php
//#section#[header]
// Module Declaration
$moduleID = 248;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Platform classes
use \API\Platform\importer;
use \API\Platform\engine;

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
importer::import("UI", "Presentation");
importer::import("DEV", "WebEngine");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\WebEngine\sdk\webObject;
use \DEV\WebEngine\webCoreProject;

if (engine::isPost())
{
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Validate form post
	if (!simpleForm::validate())
	{
		// Add form post error header
		$err_header = DOM::create("div", "ERROR");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.invalidate"));
		
		return $errFormNtf->getReport();
	}
	
	// Commit SDK Object
	$sdkObj = new webObject($_POST['lib'], $_POST['pkg'], $_POST['ns'], $_POST['oid']);
	$status = $sdkObj->remove();

	// If there is an error in creating the folder, show it
	if ($status !== TRUE)
	{
		$err_header = moduleLiteral::get($moduleID, "hd_deleteObject");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", $status));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}


// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_deleteObject");
$frame->build($title, "", FALSE)->engageModule($moduleID, "deleteObject");
$form = $frame->getFormFactory();

// Project ID
$input = $form->getInput("hidden", "id", webCoreProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);
// Object Library
$input = $form->getInput($type = "hidden", $name = "lib", $value = $_GET['lib']);
$frame->append($input);
// Object Package
$input = $form->getInput($type = "hidden", $name = "pkg", $value = $_GET['pkg']);
$frame->append($input);
// Object Namespace
$input = $form->getInput($type = "hidden", $name = "ns", $value = $_GET['ns']);
$frame->append($input);
// Object Name
$input = $form->getInput($type = "hidden", $name = "oid", $value = $_GET['oid']);
$frame->append($input);

// Header
$title = moduleLiteral::get($moduleID, "lbl_deleteObject");
$hd = DOM::create("h2", $title);
$frame->append($hd);

$path = $_GET['lib']."/".$_GET['pkg']."/".(empty($_GET['ns']) ? "" : $_GET['ns']."/").$_GET['oid'];
$p = DOM::create("h4", $path);
$frame->append($p);

// Return the report
return $frame->getFrame();
//#section_end#
?>