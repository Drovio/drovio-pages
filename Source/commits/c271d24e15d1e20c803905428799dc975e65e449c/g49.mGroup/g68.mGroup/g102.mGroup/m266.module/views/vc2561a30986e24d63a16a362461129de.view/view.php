<?php
//#section#[header]
// Module Declaration
$moduleID = 266;

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
importer::import("DEV", "Apps");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Literals\literal;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Apps\views\appView;
use \DEV\Apps\views\appViewManager;

$appID = engine::getVar('id');
if (engine::isPost())
{	
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	if (empty($_POST['name']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::dictionary("name");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
		
	// Create View
	$parent = ($_POST['parent'] == -1 ? "" : $_POST['parent']);
	$view = new appView($appID, $parent);
	$name = str_replace(" ", "_", trim($_POST['name']));
	$result = $view->create($name);
	
	
	// If there is an error in creating the library, show it
	if (!$result)
	{
		$err_header = literal::dictionary("name");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating view..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the frame
$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "lbl_createViewTitle");
$frame->build($title, "", FALSE)->engageModule($moduleID, "createView");
$form = $frame->getFormFactory();

// Application ID
$input = $form->getInput($type = "hidden", $name = "id", $value = $appID, $class = "", $autofocus = TRUE);
$frame->append($input);

// Folder Parent
$folderResources = array();
$folderResources["-1"] = "/";
$vman = new appViewManager($appID);
$viewFolders = $vman->getFolders("", TRUE);
foreach ($viewFolders as $fl)
	$folderResources[$fl] = $fl;
ksort($folderResources);

$title = moduleLiteral::get($moduleID, "lbl_folderParent");
$label = $form->getLabel($title);
$input = $form->getResourceSelect($name = "parent", $multiple = FALSE, $class = "", $folderResources, $selectedValue = "");
$formRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($formRow);

// View Name
$title = literal::dictionary("name");
$input = $form->getInput($type = "text", $name = "name", $value = "", $class = "", $autofocus = TRUE);
$formRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($formRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>