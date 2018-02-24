<?php
//#section#[header]
// Module Declaration
$moduleID = 90;

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
importer::import("API", "Model");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Model\sql\dbQuery; 
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\dataGridList;
use \SYS\Comm\db\dbConnection;

$accountId = engine::getVar('account_id');
$dbc = new dbConnection();

// get active user groups
$dbq = new dbQuery("999274607", "security.privileges.user");
$attr = array();
$groups = $dbc->execute($dbq, $attr);

$groupNames = array();
while ($row = $dbc->fetch($groups))
	$groupNames[$row['id']] = $row['name'];
	
// get participation of user active groups
$dbq = new dbQuery("2085666253", "security.privileges.accounts");
$attr = array();
$attr['id'] = $accountId;
$groups = $dbc->execute($dbq, $attr);

$userParticipation = array();
while ($row = $dbc->fetch($groups))
	$userParticipation[$row['id']] = $row['name'];
	
if (engine::isPost())
{
	$oldGroups = $userParticipation;
	$newGroups = (is_array($_POST['grant']) ? $_POST['grant'] : array() );
	
	$grant = array_keys($newGroups);
	$revoke = array_diff(array_values($userParticipation), $grant);
	
	foreach ($revoke as $key => $gname)
	{	
		// Revoke Group Privileges
		$dbq = new dbQuery("509989320", "security.privileges.accounts");
		
		$attr = array();
		$attr['account_id'] = $accountId;
		$attr['groupName'] = $gname;
		$result = $dbc->execute($dbq, $attr);
		
		// Check For error In Query execution
		if(is_bool($result) && !$result)
		{
			//error exists
			$formNotification = new formNotification();
			$formNotification->build("error");
			
			// Description
			$message = 'Revoke Group Privileges Error';
			$formNotification->appendCustomMessage($message);
			
			return $formNotification->getReport(FALSE);		
		}
	}
	
	foreach ($grant as $key => $gname)
	{
		
		// Grant Group Privileges
		$dbq = new dbQuery("988949753", "security.privileges.accounts");
		
		$attr = array();
		$attr['account_id'] = $accountId;
		$attr['groupName'] = $gname;
		$result = $dbc->execute($dbq, $attr);
		
		// Check For error In Query execution
		if(is_bool($result) && !$result)
		{
			//error exists
			$formNotification = new formNotification();
			$formNotification->build("error");
			
			// Description
			$message = 'Grant Group Privileges Error';
			$formNotification->appendCustomMessage($message);
			
			return $formNotification->getReport(FALSE);		
		}
	}
	
	$successNotification = new formNotification();
	$successNotification->build("success");
	
	// Description
	$message = $successNotification->getMessage( "success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport(FALSE);
}

// Create Module Page
$HTMLContentBuilder = new MContent($moduleID);

$simpleForm = new simpleForm();
$userGroupsForm = $simpleForm->build($moduleID, "groupsInfo", $controls = TRUE); 

// Appent form to content
$globalContainer = $HTMLContentBuilder->buildElement($simpleForm->get())->get();
 
// userId (Hidden)
$input = $simpleForm->getInput("hidden", "account_id", $accountId, $class = "", $autofocus = FALSE);
$simpleForm->append($input);

// Form Header
$attr = array();
$attr['id'] = $accountId;
$headerContent = moduleLiteral::get($moduleID, "lbl_groupsInfo", $attr);
$header = DOM::create('h2', $headerContent, "", "lhd hd2");
$simpleForm->append($header);

$formContentWrapper = DOM::create("div", "", "", "userGroupsFormContents");
$simpleForm->append($formContentWrapper);

// Create grid
$dtGridList = new dataGridList();
$glist = $dtGridList->build("", TRUE)->get();
DOM::append($formContentWrapper, $glist);

$headers = array();
$headers[] = "Group";

$dtGridList->setHeaders($headers);

// Present Info
foreach ($groupNames as $id => $gname)
{
	$gridRow = array();
	$gridRow[] = DOM::create("span", $gname);
	
	$dtGridList->insertRow($gridRow, 'grant['.$gname.']', isset($userParticipation[$id]));
}

// Return output
return $HTMLContentBuilder->getReport();
//#section_end#
?>