<?php
//#section#[header]
// Module Declaration
$moduleID = 407;

// Inner Module Codes
$innerModules = array();
$innerModules['devHome'] = 100;
$innerModules['landingPage'] = 397;

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
importer::import("DEV", "Profiler", "mlogger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\mlogger;
use \DEV\Profiler\mlogger as logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Profile\account;
use \API\Security\akeys\apiKey;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;

// Build Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build page
$pageTitle = $page->getLiteral("title", array(), FALSE);
$page->build($pageTitle, "roadmapPageContainer dev-domain", TRUE, TRUE);
$sidebarContainer = HTML::select(".roadmapPage .dev-sidebar")->item(0);

// Load navigation bar on mainpage
$navBar = HTML::select(".roadmapPage .dev-mainpage .navbar")->item(0);
$navigationBar = $page->loadView($innerModules['devHome'], "navigationBar");
DOM::append($navBar, $navigationBar);

// Load sidebar
$sidebar = $page->loadView($innerModules['devHome'], "sidebar");
DOM::append($sidebarContainer, $sidebar);

// Check if is admin enough to edit (add, remove, etc)
$coreAdmin = apiKey::validateGroupName($groupName = "PROJECT_ADMIN", $accountID = account::getInstance()->getAccountID(), $teamID = NULL, $projectID = 1);
$pagesAdmin = apiKey::validateGroupName($groupName = "PROJECT_ADMIN", $accountID = account::getInstance()->getAccountID(), $teamID = NULL, $projectID = 2);
$roadmapAdmin = ($coreAdmin || $pagesAdmin);

$rdFormContainer = HTML::select(".rdFormContainer")->item(0);
if ($roadmapAdmin)
{
	// Create form to create new roadmap item
	$form = new simpleForm();
	$newRoadmapForm = $form->build()->engageModule($moduleID, "createRoadmap")->get();
	DOM::append($rdFormContainer, $newRoadmapForm);
	
	$input = $form->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = TRUE, $required = TRUE);
	$form->insertRow("Title", $input, $required = TRUE, $notes = "");
	
	$input = $form->getTextarea($name = "description", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
	$form->insertRow("Description", $input, $required = TRUE, $notes = "");
	
	$input = $form->getInput($type = "text", $name = "hashtag", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
	$form->insertRow("Hashtag", $input, $required = TRUE, $notes = "To refer to this part of the roadmap as #hashtag");
	
	$input = $form->getInput($type = "date", $name = "date_expected", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
	$form->insertRow("Date expected", $input, $required = TRUE, $notes = "");
}
else
	HTML::remove($rdFormContainer);

// Load roadmap items
$rdListContainer = HTML::select(".rdListContainer")->item(0);
$mContainer = $page->getModuleContainer($moduleID, $viewName = "roadmapList", $attr = array(), $startup = TRUE, $containerID = "roadmapList", $loading = FALSE, $preload = TRUE);
DOM::append($rdListContainer, $mContainer);

// Load footer bar on mainpage
$docContainer = HTML::select(".roadmapPage .dev-mainpage .docContainer")->item(0);
$footerBar = $page->loadView($innerModules['landingPage'], "footerBar");
DOM::append($docContainer, $footerBar);

return $page->getReport();
//#section_end#
?>