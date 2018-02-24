<?php
//#section#[header]
// Module Declaration
$moduleID = 343;

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
importer::import("API", "Model");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\frames\dialogFrame;

if (engine::isPost())
{	
	$has_error = FALSE;
	$formErrorNotification = new formErrorNotification();
	$formErrorNotification->build();
	
	// Check Title
	if (empty($_POST['title']))
	{
		$has_error = TRUE;
						
		// Header
		$header = moduleLiteral::get($moduleID, "lbl_userGroupName");
		$err_header = $formErrorNotification->addHeader($header);
		$formErrorNotification->addDescription($err_header, "err.required");
	}
	
	// If error, show notification
	if ($has_error)
		return $formErrorNotification->getReport();
	
	$formNotification = new formNotification();
	
	// Create User Group
	$dbc = new dbConnection();
	$dbq = module::getQuery($moduleID, "new_user_group");
	$attr = array();
	$attr['name'] = engine::getVar('title');
	$success = $dbc->execute($dbq, $attr);
		
	if ($success)
	{
		$formNotification->build("success");
		$message = $formNotification->getMessage( "success", "success.save_success");
		$formNotification->appendCustomMessage($message);
	}
	else
	{
		$formNotification->build("error");
		$message = $formNotification->getMessage("error", "err.save_error");
		$formNotification->appendCustomMessage($message);
	}
	
	return $formNotification->getReport(FALSE);
}

// Build Frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_createUserGroup", array(), FALSE);
$frame->build($title)->engageModule($moduleID, "createUserGroup");
$form = $frame->getFormFactory();

// Title
$title = moduleLiteral::get($moduleID, "lbl_groupName"); 
$input = $form->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = FALSE);
$fRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($fRow);

// Return frame
return  $frame->getFrame();
//#section_end#
?>