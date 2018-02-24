<?php
//#section#[header]
// Module Declaration
$moduleID = 369;

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
importer::import("DEV", "Projects");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Connect\invitations;
use \API\Literals\moduleLiteral;
use \API\Profile\team;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \DEV\Projects\project;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "myInvitationsContainer", TRUE);

// Get pending invitations
$invitations = invitations::getAccountInvitations();
if (count($invitations) > 0)
{
	// Clean the container
	$invitationsContainer = HTML::select(".myInvitationsContainer .myInvitations")->item(0);
	HTML::innerHTML($invitationsContainer, "");
	
	// Show all invitations
	foreach ($invitations as $inviteInfo)
	{
		$refID = "iv_".mt_rand();
		$ivrow = DOM::create("div", "", $refID, "ivrow");
		DOM::append($invitationsContainer, $ivrow);
		
		if ($inviteInfo['type'] == invitations::TEAM_TYPE)
		{
			$teamInfo = team::info($inviteInfo['context']);
			$attr = array();
			$attr['tname'] = $teamInfo['name'];
			$title = moduleLiteral::get($moduleID, "lbl_team_invitation", $attr);
		}
		else
		{
			$project = new project($inviteInfo['context']);
			$projectInfo = $project->info();
			$attr = array();
			$attr['pname'] = $projectInfo['title'];
			$title = moduleLiteral::get($moduleID, "lbl_project_invitation", $attr);
		}
		$ivName = DOM::create("span", $title, "", "iv ivn");
		DOM::append($ivrow, $ivName);
		
		// Revoke / Delete action
		$formContainer = DOM::create("div", "", "", "iv action");
		DOM::append($ivrow, $formContainer);
		
		$form = new simpleForm();
		$deleteForm = $form->build("", FALSE)->engageModule($moduleID, "rejectInvitation")->get();
		DOM::append($formContainer, $deleteForm);
		
		// Add hidden inputs
		$input = $form->getInput($type = "hidden", $name = "email", $value = $inviteInfo['email'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "context", $value = $inviteInfo['context'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "type", $value = $inviteInfo['type'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "ref", $value = $refID, $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		// Submit button
		$title = moduleLiteral::get($moduleID, "lbl_ignore_invitation");
		$button = $form->getSubmitButton($title, $id = "", $name = "");
		HTML::addClass($button, "act_button");
		$form->append($button);
		
		
		// Resend email action
		$formContainer = DOM::create("div", "", "", "iv action");
		DOM::append($ivrow, $formContainer);
		
		$form = new simpleForm();
		$deleteForm = $form->build("", FALSE)->engageModule($moduleID, "acceptInvitation")->get();
		DOM::append($formContainer, $deleteForm);
		
		// Add hidden inputs
		$input = $form->getInput($type = "hidden", $name = "email", $value = $inviteInfo['email'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "context", $value = $inviteInfo['context'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "type", $value = $inviteInfo['type'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "ref", $value = $refID, $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "rl", $value = $inviteInfo['group_id'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		// Submit button
		$title = moduleLiteral::get($moduleID, "lbl_accept_invitation");
		$button = $form->getSubmitButton($title, $id = "", $name = "");
		HTML::addClass($button, "act_button");
		$form->append($button);
	}
}

// Return output
return $pageContent->getReport();
//#section_end#
?>