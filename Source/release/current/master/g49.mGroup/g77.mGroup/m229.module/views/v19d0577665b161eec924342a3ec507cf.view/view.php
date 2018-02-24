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
importer::import("UI", "Modules");
importer::import("DEV", "BugTracker");
//#section_end#
//#section#[code]
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \DEV\BugTracker\bugTracker;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Bug Priority
	if (empty($_POST['priority']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_status", FALSE);
		$err = $errFormNtf->addErrorHeader("bugStatus_h", $err_header);
		$errFormNtf->addErrorDescription($err, "bugStatus_desc", $errFormNtf->getErrorMessage("err.required"));
	}
	
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Assign solution
	$bugger = new bugTracker($_POST['pid']);
	$success = $bugger->setPriority($_POST['bid'], $_POST['priority']);
	
	if($success)
	{
		// SUCCESS NOTIFICATION
		$successFormNtf = new formNotification();
		$successFormNtf->build("success");
		
		// Description
		$message = $successFormNtf->getMessage( "success", "success.save_success");
		$successFormNtf->appendCustomMessage($message);
		
		return $successFormNtf->getReport(FALSE);
	}
	else
	{
		//On create error	
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not save";
					 		
		// ERROR NOTIFICATION
		$errorNotification = new formNotification();
		$errorNotification->build("error");
		
		// Description
		$message= "AN ERROR OCCURED : ".$customErrMsg_desc; 
		$errorNotification->appendCustomMessage($message);
				
		return $errorNotification->getReport(FALSE);
	}
}


// Create Module Page
$MContent = new MContent($moduleID);
$actionFactory = $MContent->getActionFactory();
// Build the module 
$MContent->build("", "changeBugPriority inlineForm", TRUE);

$formContent = HTML::select('.changeBugPriority .actionForm ')->item(0);

$sForm = new simpleForm();
$sForm->build($moduleID, "changeBugPriority", FALSE);
DOM::append($formContent, $sForm->get());

// Assumed that pid and bid, as passed / maintained through module::loadView

$input = $sForm->getInput($type = "hidden", $name = "pid", $value = $_GET['pid'], $class = "", $autofocus = FALSE, $required = FALSE);
$sForm->append($input);

$input = $sForm->getInput($type = "hidden", $name = "bid", $value = $_GET['bid'], $class = "", $autofocus = FALSE, $required = FALSE);
$sForm->append($input);


$resource = bugTracker::getPriorityOptions();
$input = $sForm->getResourceSelect("priority", $multiple = FALSE, $class = "", $resource, bugTracker::PR_LOW );
$icBlock = DOM::create('div', '', '', 'icBlock');
DOM::append($icBlock, $input);
$sForm->append($icBlock);

$icBlock = DOM::create('div', '', '', 'icBlock');
$button = $sForm->getSubmitButton(moduleLiteral::get($moduleID, "btn_changeValue"));
DOM::append($icBlock, $button);
$sForm->append($icBlock);

$icBlock = DOM::create('div', '', '', 'icBlock');
$button = $sForm->getButton(literal::get("global::dictionary", "close"), "", 'inlineFormHide');
DOM::append($icBlock, $button);
$sForm->append($icBlock);

// return
return  $MContent->getReport();
//#section_end#
?>