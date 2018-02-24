<?php
//#section#[header]
// Module Declaration
$moduleID = 86;

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
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \API\Model\units\sql\dbQuery;
use \API\Model\modules\module;
use \API\Model\modules\mGroup;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\dataGridList;
use \SYS\Comm\db\dbConnection;


$userGroupId = $_REQUEST['gid'];
	
// Get User Group Privileges
$dbq = new dbQuery("1469641106", "security.privileges.user");
$dbc = new dbConnection();

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
		$dbc = new dbConnection();
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
		$dbc = new dbConnection();
		$attr['mid'] = $grant_id;
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
$HTMLContentBuilder = new MContent($moduleID); 
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

$ratios = array();
$ratios[] = 0.2;
$ratios[] = 0.8;
$dtGridList->setColumnRatios($ratios);

$headers = array();
$headers[] = "ID";
$headers[] = "Module";
$dtGridList->setHeaders($headers);

// Get All Modules
$dbq = new dbQuery("2030152463", "units.modules");
$dbc = new dbConnection();
$modules = $dbc->execute($dbq);

// Modules Privileges
while ($mdl = $dbc->fetch($modules))
{
	$gridRow = array();	
	$module_id = $mdl['id'];
	
	// Module ID
	$gridRow[] = $module_id;
	
	// Get Module full path
	$mInfo = module::info($module_id);
	$gTrail = mGroup::getTrail($mInfo['group_id']);
	$mName = module::getDirectoryName($module_id);
	$moduleFullName = $gTrail.$mName;
	$gridRow[] = $moduleFullName;
	
	// Insert Row
	$checked = isset($privileges[$mdl['id']]);
	$dtGridList->insertRow($gridRow, 'grant['.$module_id.']', $checked);
}

// Return output
return $HTMLContentBuilder->getReport();
//#section_end#
?>