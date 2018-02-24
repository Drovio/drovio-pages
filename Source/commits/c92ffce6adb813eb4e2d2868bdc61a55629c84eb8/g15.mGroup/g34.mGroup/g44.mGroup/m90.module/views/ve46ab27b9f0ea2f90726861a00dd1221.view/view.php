<?php
//#section#[header]
// Module Declaration
$moduleID = 90;

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
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \API\Model\units\sql\dbQuery; 
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\dataGridList;
use \SYS\Comm\db\dbConnection;

$dbc = new dbConnection();

$accountId = (isset($_GET['account_id']) ? $_GET['account_id'] : $_POST['accountId']);

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
	
//__________ SERVER CONTROL __________//
if ($_SERVER['REQUEST_METHOD'] == "POST")
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
$input = $simpleForm->getInput("hidden", "accountId", $accountId, $class = "", $autofocus = FALSE);
$simpleForm->append($input);

// Form Header
$headerContent = moduleLiteral::get($moduleID, "lbl_groupsInfo");
$header = DOM::create('h2', "", "", "lhd hd2");
DOM::append($header, $headerContent);
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
	//Checkbox Valeue
	$checked = FALSE;
	if (isset($userParticipation[$id]))
	{
		//DOM::attr($developer, "checked", "checked");
		$checked = TRUE;
	}
	
	$gridRow = array();
	// Name
	$gridRow[] = DOM::create("span", $gname);
	
	$dtGridList->insertRow($gridRow, 'grant['.$gname.']', $checked);
}

// Return output
return $HTMLContentBuilder->getReport();
//#section_end#
?>