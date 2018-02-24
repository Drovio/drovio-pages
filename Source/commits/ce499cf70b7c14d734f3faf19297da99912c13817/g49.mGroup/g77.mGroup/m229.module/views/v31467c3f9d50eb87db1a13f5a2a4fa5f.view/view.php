<?php
//#section#[header]
// Module Declaration
$moduleID = 229;

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
importer::import("UI", "Presentation");
importer::import("DEV", "BugTracker");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \DEV\BugTracker\bugTracker;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\frames\dialogFrame;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	//No parametres error -> Continue	 
	$bugTracker = new bugTracker($_POST['pid']);
	
	//Try to create new layout
	$success = $bugTracker->deleteBug($_POST['bid']);	
	// If error, show notification
	if (!$success )
	{
		//On error	
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not delete bug";
					 		
		// Error Notification
		$errorNtf = new formNotification();
		$errorNtf->build("error");
		
		// Description
		$message= "AN ERROR OCCURED : ".$customErrMsg_desc; 
		$errorNtf->appendCustomMessage($message);
				
		return $errorNtf->getReport(FALSE);		
	}
	
	// SUCCESS Notification
	$successNtf= new formNotification();
	$successNtf->build("success");
	
	// Description
	$message= $successNtf->getMessage( "success", "success.save_success");
	$successNtf->appendCustomMessage($message);
	
	return $successNtf->getReport(FALSE); 
}

// Build Frame
$title = moduleLiteral::get($moduleID, "lbl_deleteDialogTitle", FALSE);
$frame = new dialogFrame();
$frame->build($title, $moduleID, "deleteBug", TRUE);

// Create form
$sForm = new simpleForm(); 

$msg = DOM::create('div');
	$literal = moduleLiteral::get($moduleID, 'ntf_deletePrompt');
	DOM::append($msg, $literal);
$frame->append($msg);
// 
$input = $sForm->getInput($type = "hidden", $name = "pid", $value = $_GET['pid'], $class = "", $autofocus = FALSE);
$frame->append($input);
// 
$input = $sForm->getInput($type = "hidden", $name = "bid", $value = $_GET['bid'], $class = "", $autofocus = FALSE);
$frame->append($input);

// return
return  $frame->getFrame();
//#section_end#
?>