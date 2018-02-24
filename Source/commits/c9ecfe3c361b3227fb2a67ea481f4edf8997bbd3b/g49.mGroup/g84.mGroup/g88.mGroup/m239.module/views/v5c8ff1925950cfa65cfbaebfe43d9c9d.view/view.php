<?php
//#section#[header]
// Module Declaration
$moduleID = 239;

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
importer::import("DEV", "Core");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Core\sql\sqlQuery;
use \DEV\Core\coreProject;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Remove sql query
	$q = new sqlQuery($_POST['domain'], $_POST['qid']);
	$status = $q->remove();

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
$title = moduleLiteral::get($moduleID, "hd_deleteQuery");
$frame->build($title, "", FALSE)->engageModule($moduleID, "deleteQuery");
$form = $frame->getFormFactory();

// Header
$title = moduleLiteral::get($moduleID, "lbl_deleteQuery");
$hd = DOM::create("h2", $title);
$frame->append($hd);

$nsdomain = str_replace(".", "/", $_GET['domain']);
$q = new sqlQuery($_GET['domain'], $_GET['qid']);
$title = str_replace(" ", "_", $q->getTitle());
$path = $nsdomain."/".$title.".query [".str_replace("_", ".", $_GET['qid'])."]";
$p = DOM::create("h4", $path);
$frame->append($p);

//_____ Project ID
$input = $form->getInput("hidden", "id", coreProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Query ID
$input = $form->getInput($type = "hidden", $name = "qid", $_GET['qid'], $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Domain name
$input = $form->getInput($type = "hidden", $name = "domain", $_GET['domain'], $class = "", $autofocus = FALSE);
$form->append($input);

// Return the report
return $frame->getFrame();
//#section_end#
?>