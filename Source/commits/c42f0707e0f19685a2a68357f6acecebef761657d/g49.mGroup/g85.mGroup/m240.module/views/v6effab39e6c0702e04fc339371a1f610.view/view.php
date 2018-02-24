<?php
//#section#[header]
// Module Declaration
$moduleID = 240;

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
importer::import("API", "Profile");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("DEV", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\sql\dbQuery;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Profile\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Modules\moduleGroup;
use \DEV\Modules\modulesProject;

if (engine::isPost())
{
	$has_error = FALSE;
	
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
	
	// Check Group Description
	if (empty($_POST['groupDescription']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::dictionary("title");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Proceed to create module group
	$description = $_POST['groupDescription'];
	$parent_id = $_POST['groupParent'];
	$rootParent = $_POST['rootParent'];
	if (isset($rootParent))
		$parent_id = NULL;
	
	$success = moduleGroup::create($description, $parent_id);
	
	// If there is an error in creating the module group, show it
	if (!$success)
	{
		$err_header = DOM::create("span", "Module Group");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating the group..."));
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
$title = moduleLiteral::get($moduleID, "hd_newModuleGroup");
$frame->build($title, "", FALSE)->engageModule($moduleID, "newModuleGroup");
$form = $frame->getFormFactory();

// Project ID
$input = $form->getInput("hidden", "id", modulesProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

// Module Group Description
$title = literal::dictionary("title");
$input = $form->getInput($type = "text", $name = "groupDescription", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Root Group (create under root if checked)
$title = moduleLiteral::get($moduleID, "lbl_rootParent");
$input = $form->getInput($type = "checkbox", $name = "rootParent", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($inputRow);

// Get Module Groups
$dbc = new dbConnection();
$dbq = new dbQuery("785059449", "security.privileges.developer");

$attr = array();
$attr['aid'] = account::getAccountID();
$moduleGroupsRaw = $dbc->execute($dbq, $attr);

$moduleGroups = $dbc->toArray($moduleGroupsRaw, "id", "description");
$moduleGroups_depths = $dbc->toArray($moduleGroupsRaw, "id", "depth");
foreach ($moduleGroups_depths as $id => $depth)
{
	$tabs = str_repeat(" - ", $depth);
	$moduleGroups[$id] = $tabs.$moduleGroups[$id];
}

$title = moduleLiteral::get($moduleID, "lbl_groupParent");
$moduleGroupInput = $form->getResourceSelect($name = "groupParent", $multiple = FALSE, $class = "", $moduleGroups, $selectedValue = NULL);
$libRow = $form->buildRow($title, $moduleGroupInput, $required = TRUE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>