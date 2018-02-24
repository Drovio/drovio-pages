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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("DEV", "Projects");
importer::import("DEV", "Tools");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Connect\invitations;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Model\modules\mMail;
use \API\Profile\account;
use \API\Profile\team;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Presentation\dataGridList;
use \DEV\Projects\project;
use \DEV\Tools\validator;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "searchResults");

// Get project id
$projectID = engine::getVar('id');
$searchQ = engine::getVar('search_q');

// Search all accounts
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "search_accounts");
$attr = array();
$attr['q'] = $searchQ;
$result = $dbc->execute($q, $attr);
$accounts_all = $dbc->fetch($result, TRUE);

// Search accounts that are not included in this project
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "search_accounts_no_project");
$attr = array();
$attr['pid'] = $projectID;
$attr['q'] = engine::getVar('search_q');
$result = $dbc->execute($q, $attr);
$accounts_noProject = $dbc->fetch($result, TRUE);
if (count($accounts_noProject) > 0)
{
	// Build form
	$form = new simpleForm();
	$addMembersForm = $form->build()->engageModule($moduleID, "addMembers")->get();
	$pageContent->append($addMembersForm);
	
	// Add project id
	$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "");
	$form->append($input);
	
	// Add grid list
	$gridList = new dataGridList();
	$accountList = $gridList->build("", TRUE)->get();
	$form->append($accountList);
	
	// Set headers
	$headers = array();
	$headers[] = "ID";
	$headers[] = "Title";
	$gridList->setHeaders($headers);
	
	foreach ($accounts_noProject as $accountData)
	{
		$row = array();
		$row[] = $accountData['id'];
		$row[] = $accountData['title'];
		
		$gridList->insertRow($row, "accs[".$accountData['id'].":".$accountData['mail']."]");
	}
}
else if (count($accounts_all) > 0)
{
	// Account is already in team
	$title = moduleLiteral::get($moduleID, "lbl_account_inProject");
	$hd = DOM::create("h2", $title, "", "hd");
	$pageContent->append($hd);
}
else if (validator::validEmail($searchQ))
{
	// Create the invitation
	invitations::create($searchQ, $projectID, invitations::PROJECT_TYPE);
	
	$project = new project($projectID);
	$projectInfo = $project->info();
	
	// Send invitation email
	$attr = array();
	$attr['project_title'] = $projectInfo['title'];
	$attr['account_title'] = account::getAccountTitle();
	$attr['email'] = $searchQ;
	mMail::send("/resources/mail/invitations/project_invitation_guest.html", "Project Invitation", $searchQ, $attr);
	
	// Show notification result
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = FALSE, $timeout = TRUE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = DOM::create("h2", "Project member invited.");
	$succFormNtf->append($errorMessage);
	
	return $succFormNtf->getReport();
}
else
{
	// Create header
	$title = moduleLiteral::get($moduleID, "lbl_noResults");
	$hd = DOM::create("h2", $title, "", "hd");
	$pageContent->append($hd);
}

// Return output
return $pageContent->getReport(".searchResultsContainer");
//#section_end#
?>