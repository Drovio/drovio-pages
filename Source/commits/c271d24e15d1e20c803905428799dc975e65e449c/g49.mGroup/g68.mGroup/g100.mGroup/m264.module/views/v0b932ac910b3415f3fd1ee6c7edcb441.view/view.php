<?php
//#section#[header]
// Module Declaration
$moduleID = 264;

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
importer::import("DEV", "Apps");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Apps\source\srcPackage;
use \DEV\Apps\source\srcObject;

$appID = engine::getVar('id');
if (engine::isPost())
{
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Commit SDK Object
	$sdkObj = new srcObject($appID, $_POST['pkg'], $_POST['ns'], $_POST['oid']);
	$status = $sdkObj->remove();

	// If there is an error in creating the folder, show it
	if ($status !== TRUE)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_deleteObject");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", $status));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}


// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_deleteObject");
$frame->build($title, "", FALSE)->engageModule($moduleID, "deleteObject");
$sForm = $frame->getFormFactory();

// Header
$title = moduleLiteral::get($moduleID, "lbl_deleteObject");
$hd = DOM::create("h2", $title);
$frame->append($hd);

$path = srcPackage::LIB_NAME."/".$_GET['pkg']."/".(empty($_GET['ns']) ? "" : $_GET['ns']."/").$_GET['oid'];
$p = DOM::create("h4", $path);
$frame->append($p);

// Object Library
$input = $sForm->getInput($type = "hidden", $name = "id", $value = $appID);
$frame->append($input);

// Object Package
$input = $sForm->getInput($type = "hidden", $name = "pkg", $value = $_GET['pkg']);
$frame->append($input);

// Object Namespace
$input = $sForm->getInput($type = "hidden", $name = "ns", $value = $_GET['ns']);
$frame->append($input);

// Object Name
$input = $sForm->getInput($type = "hidden", $name = "oid", $value = $_GET['oid']);
$frame->append($input);

// Return the report
return $frame->getFrame();
//#section_end#
?>