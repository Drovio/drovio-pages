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
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("DEV", "BugTracker");
//#section_end#
//#section#[code]
use \DEV\BugTracker\bugTracker;
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\frames\dialogFrame;
use \API\Security\account;


if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
	
	$empty = (is_null($_POST['title']) || empty($_POST['title']));
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_title");
		$headerId = 'itle'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'title'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");
	}
	
	// If error, show notification
	if ($has_error)
	{	
		return $formErrorNotification->getReport();
	}
	
	$bugger = new bugTracker($_POST['pid']);	 	
	
	// Save File 
	$success = $bugger->fileBug($_POST['title'], $_POST['description'],  $_POST['type'], $_POST['severity'], $_POST['identity']);	
	if($success)
	{
		// SUCCESS NOTIFICATION
		$successNotification = new formNotification();
		$successNotification->build("success");
		
		// Description
		$message= $successNotification->getMessage( "success", "success.save_success");
		$successNotification->appendCustomMessage($message);
		
		return $successNotification->getReport(FALSE);
	}
	else
	{
		//On create error	
		$customErrMsg_hd = "";
		$customErrMsg_desc = "Could not create website";
					 		
		// ERROR NOTIFICATION
		$errorNotification = new formNotification();
		$errorNotification->build("error");
		
		// Description
		$message= "AN ERROR OCCURED : ".$customErrMsg_desc; 
		$errorNotification->appendCustomMessage($message);
				
		return $errorNotification->getReport(FALSE);
	}
	
}






// Build Frame
$dFrame = new dialogFrame();
// Header
$title = moduleLiteral::get($moduleID, "lbl_reportNewBug", FALSE);
$dFrame->build($title, $moduleID, "newBug", $background = TRUE);

// Create Content
$container = DOM::create();
$dFrame->append($container); 

// Text
$stepGuides = DOM::create('p');
$text = moduleLiteral::get($moduleID, "lbl_createGuides");
DOM::append($stepGuides, $text);
$dFrame->append($stepGuides);

$sForm = new simpleForm();

// website name
$input = $sForm->getInput($type = "hidden", $name = "pid", $value = $_GET['pid'], $class = "", $autofocus = FALSE);
$dFrame->append($input);

$title = moduleLiteral::get($moduleID, "lbl_title");
$input = $sForm->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = FALSE);
$row = $sForm->buildRow($title, $input, TRUE, moduleLiteral::get($moduleID, "nts_title", FALSE));
$dFrame->append($row);

$identity = '';
$type = "text";
if(!is_null(account::info()))
{
	$type = "hidden";
	$identity = account::getAccountID();
}
$title = moduleLiteral::get($moduleID, "lbl_email");
$input = $sForm->getInput($type, $name = "identity", $identity, $class = "", $autofocus = FALSE);
if(empty($identity))
{
	$row = $sForm->buildRow($title, $input, TRUE, moduleLiteral::get($moduleID, "nts_email", FALSE));
	$dFrame->append($row);
}
else
	$dFrame->append($input);
	
$resource = array();
$resource[bugTracker::SV_FEATURE ] = bugTracker::SV_FEATURE ;
$resource[bugTracker::SV_MINOR ] = bugTracker::SV_MINOR ;
$resource[bugTracker::SV_MAJOR ] = bugTracker::SV_MAJOR ;
$resource[bugTracker::SV_CRITICAL ] = bugTracker::SV_CRITICAL ;

$title = moduleLiteral::get($moduleID, "lbl_severity");
$input = $sForm->getResourceSelect($name = "severity", $multiple = FALSE, $class = "", $resource, $selectedValue = bugTracker::SV_MINOR);
$row = $sForm->buildRow($title, $input, TRUE, moduleLiteral::get($moduleID, "nts_severity", FALSE));
$dFrame->append($row);

$title = moduleLiteral::get($moduleID, "lbl_type");
$input = $sForm->getInput($type = "text", $name = "type", $value = "", $class = "", $autofocus = FALSE);
$row = $sForm->buildRow($title, $input, TRUE, moduleLiteral::get($moduleID, "nts_type", FALSE));
$dFrame->append($row);

$title = moduleLiteral::get($moduleID, "lbl_description");
$input = $sForm->getTextArea($name = "description", $value = "", $class = "", $autofocus = FALSE);
$row = $sForm->buildRow($title, $input, TRUE, moduleLiteral::get($moduleID, "nts_description", FALSE));
$dFrame->append($row);

// return
return  $dFrame->getFrame();
//#section_end#
?>