<?php
//#section#[header]
// Module Declaration
$moduleID = 242;

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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("DEV", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Modules\module as DEVModule;
use \DEV\Modules\modulesProject;

if (engine::isPost())
{
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Remove module query
	$mdl = new DEVModule($_POST['mid']);
	$status = $mdl->removeQuery($_POST['qid']);

	// If there is an error in creating the folder, show it
	if ($status !== TRUE)
	{
		$err_header = moduleLiteral::get($moduleID, "lbl_deleteQuery");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", $status));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}


// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_deleteModuleQuery");
$frame->build($title, "", FALSE)->engageModule($moduleID, "deleteQuery");
$form = $frame->getFormFactory();

// Project ID
$input = $form->getInput("hidden", "id", modulesProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

// Module ID
$input = $form->getInput($type = "hidden", $name = "mid", $value = $_GET['mid']);
$frame->append($input);

// Query ID
$input = $form->getInput($type = "hidden", $name = "qid", $value = $_GET['qid']);
$frame->append($input);

// Header
$title = moduleLiteral::get($moduleID, "lbl_deleteQuery");
$hd = DOM::create("h2", $title);
$frame->append($hd);

$moduleInfo = module::info($_GET['mid']);
$mdl = new DEVModule($_GET['mid']);
$queries = $mdl->getQueries();
$queryName = $queries[$_GET['qid']];
$path = $moduleInfo['module_title']."/".str_replace(" ", "_", $queryName).".query";
$p = DOM::create("h4", $path);
$frame->append($p);

// Return the report
return $frame->getFrame();
//#section_end#
?>