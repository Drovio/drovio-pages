<?php
//#section#[header]
// Module Declaration
$moduleID = 237;

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
importer::import("DEV", "Core");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \UI\Developer\devTabber;
use \UI\Developer\codeEditor;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Presentation\notification;
use \DEV\Core\ajax\ajaxPage;
use \DEV\Core\coreProject;

if (engine::isPost())
{
	$directory = $_POST['d'];
	$pageName = $_POST['name'];
	$ajaxPage = new ajaxPage($pageName, $directory);
	
	$status = $ajaxPage->update($_POST['ajaxCode']);
	
	// Build Notification
	$reportNtf = new notification();
	if ($status === TRUE)
	{
		$reportNtf->build($type = notification::SUCCESS, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = $reportNtf->getMessage("success", "success.save_success");
	}
	else if ($status === FALSE)
	{
		$reportNtf->build($type = notification::ERROR, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = $reportNtf->getMessage("error", "err.save_error");
	}
	else
	{
		$reportNtf->build($type = notification::WARNING, $header = FALSE, $timeout = FALSE, $disposable = FALSE);
		$reportMessage = DOM::create("span", "There are syntax errors in this document.");
	}
	
	$reportNtf->append($reportMessage);
	$notification = $reportNtf->get();
	
	return devTabber::getNotificationResult($notification, ($status === TRUE));
}

$form = new simpleForm("", TRUE);
$directory = $_GET['d'];
$pageName = $_GET['name'];
$ajaxPage = new ajaxPage($pageName, $directory);


// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$pageContent->build("", "ajaxPageEditor");

// Initialize Editor Form
$pageForm = $form->build("", FALSE)->engageModule($moduleID, "ajaxPageEditor")->get();
$pageContent->append($pageForm);

//_____ Project ID
$input = $form->getInput("hidden", "id", coreProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);
//_____ Directory name
$hidden = $form->getInput($type = "hidden", $name = "d", $value = $_GET['d']);
$form->append($hidden);
//_____ Page name
$hidden = $form->getInput($type = "hidden", $name = "name", $value = $_GET['name']);
$form->append($hidden);


// Create Global Container
$tlb = new navigationBar();
$globalContainer = DOM::create("div", "", "objectGlobalContainer");
$form->append($globalContainer);

// Build Toolbar
$navToolbar = $tlb->build(navigationBar::TOP, $globalContainer)->get();
DOM::append($globalContainer, $navToolbar);

// Save Button
$saveTool = DOM::create("button", "", "", "objTool save");
DOM::attr($saveTool, "type", "submit");
$tlb->insertToolbarItem($saveTool);

// Delete query
$deleteTool = DOM::create("span", "", "", "objTool delete");
$tool = $tlb->insertToolbarItem($deleteTool);
$attr = array();
$attr['id'] = coreProject::PROJECT_ID;
$attr['dir'] = $directory;
$attr['name'] = $pageName;
$actionFactory->setModuleAction($deleteTool, $moduleID, "deletePage", "", $attr);


// Load Page Code
$editor = new codeEditor();
$content = $ajaxPage->getSourceCode();
$ajaxEditor = $editor->build("php", $content, "ajaxCode")->get();
DOM::append($globalContainer, $ajaxEditor);

// Get Content
$obj_id = str_replace("/", "_", $_GET['d'])."_".$_GET['name'];
$header = $_GET['name'].".php";
$devTabber = new devTabber();
return $devTabber->getReportContent($obj_id, $header, $pageContent->get());
//#section_end#
?>