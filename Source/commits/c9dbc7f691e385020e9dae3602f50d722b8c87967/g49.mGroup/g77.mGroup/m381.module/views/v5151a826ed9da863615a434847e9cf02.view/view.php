<?php
//#section#[header]
// Module Declaration
$moduleID = 381;

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
importer::import("API", "Resources");
importer::import("DEV", "Projects");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Resources\filesystem\fileManager;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\popups\popup;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$projectID = engine::getVar("id");
$project = new project($projectID);

// Build the module content
$pageContent->build("", "projectImageEditorDialogContainer", TRUE);

if (engine::isPost())
{
	// Update team profile image
	if (!empty($_FILES['project_icon']))
	{
		$icon = fileManager::get($_FILES['project_icon']['tmp_name']);
		$project->updateProjectIcon($icon);
		
		return $actionFactory->getReportReload(TRUE);
	}
}

$formContainer = HTML::select(".projectIconEditorDialog .formContainer")->item(0);
if (isset($projectID))
{
	// Get team info and check for image url
	$projectIconUrl = $project->getProjectIconUrl();
	if (isset($projectIconUrl))
	{
		$imageContainer = HTML::select(".projectIconEditorDialog .imageContainer")->item(0);
		$img = DOM::create("img");
		DOM::attr($img, "src", $projectIconUrl);
		DOM::append($imageContainer, $img);
	}
	
	// Build form
	$form = new simpleForm("");
	$imageForm = $form->build($action = "", $defaultButtons = TRUE, $async = TRUE, $fileUpload = TRUE)->engageModule($moduleID, "ProjectIconEditorDialog")->get();
	DOM::append($formContainer, $imageForm);
	
	// Image type
	$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "");
	$form->append($input);
	
	// Team profile image
	$title = moduleLiteral::get($moduleID, "lbl_project_icon");
	$notes = moduleLiteral::get($moduleID, "lbl_project_icon_notes");
	$input = $form->getFileInput($name = "project_icon", $class = "", $required = TRUE, $accept = ".png");
	$form->insertRow($title, $input, $required = TRUE, $notes);
}
else
{
	$header = DOM::create("h2", "Request error! Please try again.", "", "header");
	DOM::append($formContainer, $header);
}

// Create popup
$pp = new popup();
$pp->type($type = popup::TP_PERSISTENT, $toggle = FALSE);
$pp->background(TRUE);
$pp->build($pageContent->get());

return $pp->getReport();
//#section_end#
?>