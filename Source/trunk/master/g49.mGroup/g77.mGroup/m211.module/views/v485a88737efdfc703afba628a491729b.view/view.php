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
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Connect\invitations;
use \API\Literals\moduleLiteral;
use \API\Profile\team;
use \API\Security\accountKey;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "invitationListContainer", TRUE);

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

// Add invitations pending
$pendingInvitations = invitations::getInvitations($projectID, $type = invitations::PROJECT_TYPE);
$invitationsContainer = HTML::select(".invitationList")->item(0);
if (count($pendingInvitations) > 0)
	HTML::innerHTML($invitationsContainer, "");
foreach ($pendingInvitations as $inviteInfo)
{
	$refID = "iv_".mt_rand();
	$ivrow = DOM::create("div", "", $refID, "ivrow");
	DOM::append($invitationsContainer, $ivrow);
	
	$ivName = DOM::create("span", $inviteInfo['email'], "", "iv ivn");
	DOM::append($ivrow, $ivName);
	
	if (!$projectAdmin)
		continue;
	
	// Revoke / Delete action
	$formContainer = DOM::create("div", "", "", "iv action");
	DOM::append($ivrow, $formContainer);
	
	$form = new simpleForm();
	$deleteForm = $form->build("", FALSE)->engageModule($moduleID, "revokeInvitation")->get();
	DOM::append($formContainer, $deleteForm);
	
	// Add hidden inputs
	$input = $form->getInput($type = "hidden", $name = "email", $value = $inviteInfo['email'], $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	$input = $form->getInput($type = "hidden", $name = "context", $value = $inviteInfo['context'], $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	$input = $form->getInput($type = "hidden", $name = "id", $value = $inviteInfo['context'], $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	$input = $form->getInput($type = "hidden", $name = "type", $value = $inviteInfo['type'], $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	$input = $form->getInput($type = "hidden", $name = "ref", $value = $refID, $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	// Submit button
	$title = moduleLiteral::get($moduleID, "lbl_delete_invitation");
	$button = $form->getSubmitButton($title, $id = "", $name = "");
	HTML::addClass($button, "act_button");
	$form->append($button);
	
	
	// Resend email action
	$formContainer = DOM::create("div", "", "", "iv action");
	DOM::append($ivrow, $formContainer);
	
	$form = new simpleForm();
	$deleteForm = $form->build("", FALSE)->engageModule($moduleID, "resendInvitation")->get();
	DOM::append($formContainer, $deleteForm);
	
	// Add hidden inputs
	$input = $form->getInput($type = "hidden", $name = "email", $value = $inviteInfo['email'], $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	$input = $form->getInput($type = "hidden", $name = "context", $value = $inviteInfo['context'], $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	$input = $form->getInput($type = "hidden", $name = "id", $value = $inviteInfo['context'], $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	$input = $form->getInput($type = "hidden", $name = "type", $value = $inviteInfo['type'], $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	// Submit button
	$title = moduleLiteral::get($moduleID, "lbl_resend_invitation");
	$button = $form->getSubmitButton($title, $id = "", $name = "");
	HTML::addClass($button, "act_button");
	$form->append($button);
}

// Return output
return $pageContent->getReport();
//#section_end#
?>