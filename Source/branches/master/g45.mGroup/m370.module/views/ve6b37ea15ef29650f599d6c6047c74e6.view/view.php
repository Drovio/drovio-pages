<?php
//#section#[header]
// Module Declaration
$moduleID = 370;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Connect\invitations;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \API\Profile\team;
use \API\Profile\account;
use \API\Security\accountKey;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get current team id
$teamID = team::getTeamID();

// Get whether the account is team admin
$teamAdmin = accountKey::validateGroup($groupName = "TEAM_ADMIN", $context = $teamID, $type = accountKey::TEAM_KEY_TYPE);

$pageContent->build("", "teamMembersViewer", TRUE);
$whiteBox = HTML::select(".teamMembersViewer .whiteBox")->item(0);

// Set navigation
$nav = array();
$nav["members"] = "memberList";
$nav["invitations"] = "invitationList";
foreach ($nav as $class => $viewName)
{
	$ref = $class."_ref";
	$navItem = HTML::select(".teamMembers .menu .menu_item.".$class)->item(0);
	$pageContent->setStaticNav($navItem, $ref, $targetcontainer = "membersContainer", $targetgroup = "mGroup", $navgroup = "mGroup", $display = "none");
	
	$mContainer = $pageContent->getModuleContainer($moduleID, $viewName, $attr = array(), $startup = TRUE, $ref, $loading = FALSE, $preload = TRUE);
	DOM::append($whiteBox, $mContainer);
	$pageContent->setNavigationGroup($mContainer, "mGroup");
}

// Check team admin and remove new member button
$invitationButton = HTML::select(".teamMembers .wbutton.newMember")->item(0);
if ($teamAdmin)
{
	// Set invitation dialog to button
	$attr = array();
	$attr['tid'] = $teamID;
	$actionFactory->setModuleAction($invitationButton, $moduleID, "invitationDialog", "", $attr);
}
else
	HTML::replace($invitationButton, NULL);


// Return output
return $pageContent->getReport();
//#section_end#
?>