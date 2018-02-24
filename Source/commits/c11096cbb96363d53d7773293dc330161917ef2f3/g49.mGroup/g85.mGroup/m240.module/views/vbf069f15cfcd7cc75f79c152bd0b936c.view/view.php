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
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("DEV", "Modules");
importer::import("UI", "Forms");
importer::import("UI", "Interactive");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Model\modules\mGroup;
use \API\Model\modules\module as APIModule;
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \API\Profile\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Interactive\forms\formAutoComplete;
use \DEV\Modules\module;
use \DEV\Modules\modulesProject;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Module Title
	$empty = (is_null($_POST['queryTitle']) || empty($_POST['queryTitle']));
	if (empty($_POST['queryTitle']))
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
	
	// Create new view
	$module = new module($_POST['parent_id']);
	$success = $module->createQuery($_POST['queryTitle']);
	
	// If there is an error in creating the module group, show it
	if (!$success)
	{
		$err_header = DOM::create("span", "Module Query Creation");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating the module query..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_newModuleQuery");
$frame->build($title, "", FALSE)->engageModule($moduleID, "newModuleQuery");
$form = $frame->getFormFactory();

// Project ID
$input = $form->getInput($type = "hidden", $name = "id", $value = modulesProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);

// View Title
$title = literal::dictionary("title");
$input = $form->getInput($type = "text", $name = "queryTitle", $value = "", $class = "", $autofocus = FALSE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);



// Get Module Groups
$moduleGroupsRaw = mGroup::getAllGroups();
foreach ($moduleGroupsRaw as $info)
{
	$moduleGroups[$info['id']] = $info['description'];
	$moduleGroups_depths[$info['id']] = $info['depth'];
}
foreach ($moduleGroups_depths as $id => $depth)
{
	$tabs = str_repeat(" - ", $depth);
	$moduleGroups[$id] = $tabs.$moduleGroups[$id];
}

$title = moduleLiteral::get($moduleID, "lbl_moduleGroup");
$moduleGroupInput = $form->getResourceSelect($name = "moduleGroup", $multiple = FALSE, $class = "", $moduleGroups, $selectedValue = NULL);
$libRow = $form->buildRow($title, $moduleGroupInput, $required = TRUE, $notes = "");
$frame->append($libRow);


// Get group modules
$moduleParents = APIModule::getAllModules(array_shift(array_keys($moduleGroups)));
foreach ($moduleParents as $info)
	$moduleParents_resource[$info['id']] = $info['title'];

$title = moduleLiteral::get($moduleID, "lbl_moduleParent");
$moduleParentsSelect = $form->getResourceSelect($name = "parent_id", $multiple = FALSE, $class = "", $moduleParents_resource, $selectedValue = NULL);
$parRow = $form->buildRow($title, $moduleParentsSelect, $required = TRUE, $notes = "");
$frame->append($parRow);

// autocomplete
$populate = array();
$populate[] = DOM::attr($moduleParentsSelect, "id");
$path = "/ajax/modules/groupModules.php";

formAutoComplete::engage($moduleGroupInput, $path, array(), array(), $populate, "lenient");

// Return the report
return $frame->getFrame();
//#section_end#
?>