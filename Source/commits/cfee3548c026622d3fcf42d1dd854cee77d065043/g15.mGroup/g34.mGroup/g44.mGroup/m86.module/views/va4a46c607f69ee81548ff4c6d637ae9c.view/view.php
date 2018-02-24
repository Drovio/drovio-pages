<?php
//#section#[header]
// Module Declaration
$moduleID = 86;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\dataGridList;

if ($_SERVER['REQUEST_METHOD'] == "GET")
	$userGroupId = $_GET['gid'];
else if ($_SERVER['REQUEST_METHOD'] == "POST")
	$userGroupId = $_POST['gid'];
	
// Get User Group Privileges
$dbq = new dbQuery("1469641106", "security.privileges.user");
$dbc = new interDbConnection();

$attr = array();
$attr['gid'] = $userGroupId;
$userGrpModules = $dbc->execute($dbq, $attr);

$privileges = array();
while ($prv = $dbc->fetch($userGrpModules))
	$privileges[$prv['id']] = 1;	
	
	
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Get Grant Privileges
	$postGrant = $_POST['grant'];
	
	// Get privileges to revoke
	$revoke = array();
	foreach ($privileges as $key => $value)
		if (!isset($postGrant[$key]))
			$revoke[] = $key;
	
	// Get privileges to grant
	$grant = array();
	foreach ($postGrant as $key => $value)
		if (!isset($privileges[$key]))
			$grant[] = $key;
	
	// Revoke Privileges
	if(!empty($revoke))
	{
		// sql can not take empty array as argument
		// if array is empty there is nothing to revoke, so skip the guery
		$dbq = new dbQuery("539161284", "security.privileges");
		$dbc = new interDbConnection();
		$attr = array();
		$attr['ids'] = implode(",", $revoke);
		$attr['gid'] = $userGroupId;
		$result = $dbc->execute($dbq, $attr);
	}
	else
	{
		$result = array();
	}
	
	// Check For error In Query execution
	if(is_bool($result) && !$result)
	{
		//error exists
		$formNotification = new formNotification();
		$formNotification->build("error");
		
		// Description
		$message = 'Revoke Privileges Error';
		$formNotification->appendCustomMessage($message);
		
		return $formNotification->getReport(FALSE);		
	}
	
	// Grant Privileges
	$attr = array();
	$attr['gid'] = $userGroupId;
	foreach ($grant as $grant_id)
	{
		$dbq = new dbQuery("168706013", "security.privileges");
		$dbc = new interDbConnection();
		$attr['mid'] = $grant_id;
		print_r($attr);
		$result = $dbc->execute($dbq, $attr);
		
		// Check For error In Query execution
		if(is_bool($result) && !$result)
		{
			//error exists
			$formNotification = new formNotification();
			$successNotification->build("error");
			
			// Description
			$message = 'Grant Privileges Error';
			$formNotification->appendCustomMessage($message);
			
			return $formNotification->getReport(FALSE);			
		}
	}	
	
	// Success
	$formNotification= new formNotification();
	$formNotification->build("success");
	
	// Description
	$message= $formNotification->getMessage("success", "success.save_success");
	$formNotification->appendCustomMessage($message);
	
	return $formNotification->getReport(FALSE);
}

// Create Module Page
$HTMLContentBuilder = new HTMLContent(); 
$simpleForm = new simpleForm();
$userGroupPrivilegesForm = $simpleForm->build($moduleID, "privilegesInfo", $controls = TRUE); 

// Appent form to content
$globalContainer = $HTMLContentBuilder->buildElement($simpleForm->get())->get();
 
// userGroup (Hidden)
$input = $simpleForm->getInput("hidden", "gid", $userGroupId, $class = "", $autofocus = FALSE);
$simpleForm->append($input);
// Form Header
$headerContent = moduleLiteral::get($moduleID, "lbl_userGroupInfo");
$header = DOM::create('h2', "", "", "lhd hd2");
DOM::append($header, $headerContent);
$simpleForm->append($header);
$formContentWrapper = DOM::create("div", "", "", "privilegesGroupSection");
$simpleForm->append($formContentWrapper);

// Create grid
$dtGridList = new dataGridList();
$glist = $dtGridList->build("", TRUE)->get();
DOM::append($formContentWrapper, $glist);

$headers = array();
$headers[] = "ID";
$headers[] = "Group";
$headers[] = "Module";
$headers[] = "Scope";

$dtGridList->setHeaders($headers);

// Get All Modules
$dbq = new dbQuery("2030152463", "units.modules");
$dbc = new interDbConnection();
$modules = $dbc->execute($dbq);

// Modules Privileges
while ($mdl = $dbc->fetch($modules))
{
	$gridRow = array();	
	$module_id = $mdl['id'];
	
	// Checkbox value
	$checked = FALSE;
	if (isset($privileges[$mdl['id']]))
		$checked = TRUE;
	
	// Module ID
	$gridRow[] = DOM::create("span", $mdl['id']);
	
	// Group Title
	$gridRow[] = DOM::create("span", $mdl['group_description']);
	
	// Module Title
	$gridRow[] = DOM::create("span", $mdl['title']);
	
	// Module Scope
	$gridRow[] = DOM::create("span", $mdl['scope']);
	
	// Insert Row
	$dtGridList->insertRow($gridRow, 'grant['.$module_id.']', $checked);
}

// Return output
return $HTMLContentBuilder->getReport();
//#section_end#
?>