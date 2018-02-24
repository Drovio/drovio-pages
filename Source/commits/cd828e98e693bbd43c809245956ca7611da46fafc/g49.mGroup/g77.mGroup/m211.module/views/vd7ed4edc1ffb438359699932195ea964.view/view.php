<?php
//#section#[header]
// Module Declaration
$moduleID = 211;

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
importer::import("API", "Connect");
importer::import("API", "Literals");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Connect\invitations;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \API\Security\accountKey;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;
use \DEV\Projects\project;

$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();


// Get project data
$projectID = $projectInfo['id'];
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Get whether the account is team admin
$projectAdmin = accountKey::validateGroup($groupName = "PROJECT_ADMIN", $context = $projectID, $type = accountKey::PROJECT_KEY_TYPE);

// Build module page
$title = moduleLiteral::get($moduleID, "lbl_projectMemberManager", array(), FALSE);
$page->build($title." | ".$projectTitle, "projectMemberManager", TRUE);
$whiteBox = HTML::select(".projectMembers .whiteBox")->item(0);

// Set navigation
$nav = array();
$nav["members"] = "memberList";
$nav["invitations"] = "invitationList";
foreach ($nav as $class => $viewName)
{
	$ref = $class."_ref";
	$navItem = HTML::select(".projectMembers .menu .menu_item.".$class)->item(0);
	$page->setStaticNav($navItem, $ref, $targetcontainer = "membersContainer", $targetgroup = "mGroup", $navgroup = "mGroup", $display = "none");
	
	$attr = array();
	$attr['id'] = $projectID;
	$mContainer = $page->getModuleContainer($moduleID, $viewName, $attr, $startup = TRUE, $ref, $loading = FALSE, $preload = TRUE);
	DOM::append($whiteBox, $mContainer);
	$page->setNavigationGroup($mContainer, "mGroup");
}

// Check team admin and remove new member button
$invitationButton = HTML::select(".projectMembers .wbutton.newMember")->item(0);
if ($projectAdmin)
{
	// Set invitation dialog to button
	$attr = array();
	$attr['id'] = $projectID;
	$actionFactory->setModuleAction($invitationButton, $moduleID, "invitationDialog", "", $attr);
}
else
	HTML::replace($invitationButton, NULL);

// Return output
$holder = engine::getVar('holder');
return $page->getReport($holder);
//#section_end#
?>