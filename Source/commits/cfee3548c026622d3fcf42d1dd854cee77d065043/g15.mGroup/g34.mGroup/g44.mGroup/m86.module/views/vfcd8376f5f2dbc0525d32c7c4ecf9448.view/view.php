<?php
//#section#[header]
// Module Declaration
$moduleID = 86;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Notifications");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery; 
use \API\Resources\literals\moduleLiteral;
use \API\Resources\forms\inputValidator;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\frames\windowFrame; 

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Clear report
	report::clear();
	
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
	
	// Check Title
	$empty = is_null($_POST['title']) || empty($_POST['title']);
	if ($empty)
	{
		$has_error = TRUE;
						
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_userGroupName");
		$headerId = 'userGroupName'.'ErrorHeader';
		$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
		// Description
		$description = "err.required";
		$descriptionId = 'userGroupName'.'ErrorDescription';
		$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");
	}
	
	// If error, show notification
	if ($has_error)
	{
		report::clear();		
		return $formErrorNotification->getReport();
	}
	
	$formNotification = new formNotification();
	
	// Create User Group
	$dbq = new dbQuery("1149534366", "security.privileges.user");
	$dbc = new interDbConnection();
	$attr = array();
	$attr['name'] = $_POST['title'];
	$success = $dbc->execute($dbq, $attr);
		
	if ($success)
	{
		$formNotification->build("success");	
		// Description
		$message = $formNotification->getMessage( "success", "success.save_success");
		$formNotification->appendCustomMessage($message);
	}
	else
	{
		$formNotification->build("error");	
		// Description
		$message = $formNotification->getMessage("error", "err.save_error");
		$formNotification->appendCustomMessage($message);
	}
	
	report::clear();
	return $formNotification->getReport(FALSE);
}

// Build Frame
$frame = new windowFrame();
// Header
$hd = moduleLiteral::get($moduleID, "lbl_createUserGroup", FALSE);

$frame->build($hd);

// Create container
$container = DOM::create();

// Create form
$simpleForm = new simpleForm();
$simpleForm->build($moduleID, "createUserGroup", $controls = TRUE);

// Append Form
DOM::append($container, $simpleForm->get());

// Title
$title = moduleLiteral::get($moduleID, "lbl_userGroupName"); 
$input = $simpleForm->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = FALSE);
$simpleForm->insertRow($title, $input, $required = TRUE, $notes = "");

// Append Container to Frame
$frame->append($container);
// return
return  $frame->getFrame();
//#section_end#
?>