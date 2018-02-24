<?php
//#section#[header]
// Module Declaration
$moduleID = 211;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;
use \DEV\Projects\project;

$pageContent = new MPage($moduleID);
$title = moduleLiteral::get($moduleID, "lbl_projectMemberManager", array(), FALSE);
$pageContent->build($title, "projectMemberManager", TRUE);

// Get project id and name
$projectID = $_REQUEST['id'];

// Get project info
$project = new project($projectID);
$projectInfo = $project->info();
	
// Get project data
$projectName = $projectInfo['name'];
$projectTitle = $projectInfo['title'];

// Get container
$addMemberContainer = HTML::select(".addMember")->item(0);

// Add new member row
$form = new simpleForm();
$addMemberForm = $form->build($moduleID, "addMember", FALSE)->get();
DOM::append($addMemberContainer, $addMemberForm);

// hidden project id
$input = $form->getInput($type = "hidden", $name = "pid", $value = $projectID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Email input
$input = $form->getInput($type = "email", $name = "invitationEmail", $value = "", $class = "invitation", $autofocus = FALSE, $required = TRUE);
$form->append($input);
$ph = "newmember@example.com";
DOM::attr($input, "placeholder", $ph);

$title = moduleLiteral::get($moduleID, "lbl_inviteMember");
$submitButton = $form->getSubmitButton($title, $id = "inviteBtn");
$form->append($submitButton);


$editMembersContainer = HTML::select(".editMembers")->item(0);

$form = new simpleForm();
$editMembersForm = $form->build($moduleID, "editMembers", FALSE)->get();
DOM::append($editMembersContainer, $editMembersForm);

// hidden project id
$input = $form->getInput($type = "hidden", $name = "pid", $value = $projectID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Get members and show
$members = $project->getProjectAccounts();
foreach ($members as $member)
{
	// Build the member row
	$memberRow = DOM::create("div", "", "", "memberRow");
	$form->append($memberRow);
	
	// Remove member checkbox
	$input = $form->getInput($type = "checkbox", $name = "remove[".$member['accountID']."]", $value = "", $class = "removeCheck", $autofocus = FALSE, $required = FALSE);
	DOM::append($memberRow, $input);
	$inputID = DOM::attr($input, "id");
	
	$memberName = $member['firstname']." ".$member['lastname'];
	$label = $form->getLabel($memberName, $for = $inputID, $class = "");
	$title = DOM::create("h4", $label, "", "memberName");
	DOM::append($memberRow, $title);
}


$title = moduleLiteral::get($moduleID, "lbl_removeNotification");
$removeNotification = DOM::create("p", $title);
$form->append($removeNotification);


$title = moduleLiteral::get($moduleID, "lbl_applyChanges");
$submitButton = $form->getSubmitButton($title, $id = "applyBtn");
$form->append($submitButton);

// Return the report
return $pageContent->getReport();
//#section_end#
?>