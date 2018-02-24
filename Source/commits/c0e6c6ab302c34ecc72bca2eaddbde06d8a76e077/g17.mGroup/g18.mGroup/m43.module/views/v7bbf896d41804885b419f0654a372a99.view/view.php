<?php
//#section#[header]
// Module Declaration
$moduleID = 43;

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
importer::import("API", "Content");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Content\pgVisitsMetrics;
use \API\Resources\literals\moduleLiteral;
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
	
	$empty = (is_null($_POST['bid']) || empty($_POST['bid']));
	if ($empty)
	{
		$has_error = TRUE;
				
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_wsTitle");
		$headerId = 'wsTitle'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'wsTitle'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");
	}
	
	// If error, show notification
	if ($has_error)
	{	
		return $formErrorNotification->getReport();
	}
	
	$bugger = new pgVisitsMetrics($_POST['pid']);	 

	
	
	
	// Save File 
	$success = $bugger->solveBug($_POST['bid'], $_POST['comments'], $_POST['status']);	
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
$title = moduleLiteral::get($moduleID, "lbl_createWebsite", FALSE);
$dFrame->build($title, $moduleID, "solveIssue", $background = TRUE);

// Create Content
$container = DOM::create();
$dFrame->append($container); 

// Text
$stepGuides = DOM::create('p', $_GET['title']);
$dFrame->append($stepGuides);

$sForm = new simpleForm();

// website name
$input = $sForm->getInput($type = "hidden", $name = "pid", $value = $_GET['pid'], $class = "", $autofocus = FALSE);
$dFrame->append($input);

$input = $sForm->getInput($type = "hidden", $name = "bid", $value = $_GET['bid'], $class = "", $autofocus = FALSE);
$dFrame->append($input);


$resource = array();
$resource[pgVisitsMetrics::ST_SOLVED] = pgVisitsMetrics::ST_SOLVED;
$resource[pgVisitsMetrics::ST_REJECTED] = pgVisitsMetrics::ST_REJECTED;
//$resource[pgVisitsMetrics::ST_PENDING] = pgVisitsMetrics::ST_PENDING;
$title = DOM::create('span', 'status');
$input = $sForm->getResourceSelect($name = "status", $multiple = FALSE, $class = "", $resource, $selectedValue = pgVisitsMetrics::ST_SOLVED );
DOM::appendAttr($input, "disabled", "disabled");
$row = $sForm->buildRow($title, $input, TRUE, "");
$dFrame->append($row);


$title = DOM::create('span', 'comments');
$input = $sForm->getTextArea($name = "comments", $value = "", $class = "", $autofocus = FALSE);
$row = $sForm->buildRow($title, $input, TRUE, "");
$dFrame->append($row);

// return
return  $dFrame->getFrame();
//#section_end#
?>