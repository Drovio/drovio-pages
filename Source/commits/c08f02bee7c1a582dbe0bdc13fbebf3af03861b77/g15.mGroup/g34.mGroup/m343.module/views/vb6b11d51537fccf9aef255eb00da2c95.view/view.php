<?php
//#section#[header]
// Module Declaration
$moduleID = 343;

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
use \API\Model\modules\module;
use \API\Model\modules\mGroup;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\dataGridList;
use \SYS\Comm\db\dbConnection;

$accGroupID = engine::getVar('gid');
$dbc = new dbConnection();
	
// Get User Group Modules
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_group_modules");

$attr = array();
$attr['gid'] = $accGroupID;
$result = $dbc->execute($q, $attr);

$groupModules = array();
while ($prv = $dbc->fetch($result))
	$groupModules[$prv['id']] = 1;

if (engine::isPost())
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
	if (!empty($revoke))
	{
		$q = module::getQuery($moduleID, "remove_module_from_group");
		$attr = array();
		$attr['ids'] = implode(",", $revoke);
		$attr['gid'] = $accGroupID;
		$result = $dbc->execute($q, $attr);
	}
	
	// Grant Privileges
	$attr = array();
	$attr['gid'] = $accGroupID;
	foreach ($grant as $grantModuleID)
	{
		$q = module::getQuery($moduleID, "add_module_to_group");
		$attr['mid'] = $grantModuleID;
		$result = $dbc->execute($q, $attr);
	}	
	
	// Success
	$formNotification= new formNotification();
	$formNotification->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Description
	$message= $formNotification->getMessage("success", "success.save_success");
	$formNotification->appendCustomMessage($message);
	
	return $formNotification->getReport(FALSE);
}

// Create Module Page
$pageContent = new MContent($moduleID); 
$pageContent->build("", "groupPrivilegesEditor");

$form = new simpleForm();
$userGroupPrivilegesForm = $form->build()->engageModule($moduleID, "groupPrivilegesEditor")->get();
$pageContent->append($userGroupPrivilegesForm);
 
// userGroup (Hidden)
$input = $form->getInput("hidden", "gid", $accGroupID, $class = "", $autofocus = FALSE);
$form->append($input);

// Form Header
$title = moduleLiteral::get($moduleID, "lbl_groupPrivileges");
$header = DOM::create('h3', $title, "", "lhd");
$form->append($header);

// Create grid
$dtGridList = new dataGridList();
$glist = $dtGridList->build("groupModuleList", TRUE)->get();
$form->append($glist);

$ratios = array();
$ratios[] = 0.2;
$ratios[] = 0.8;
$dtGridList->setColumnRatios($ratios);

$headers = array();
$headers[] = "ID";
$headers[] = "Module";
$dtGridList->setHeaders($headers);

// Get All Modules
$allModules = module::getAllModules();
foreach ($allModules as $mdl)
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
	$dtGridList->insertRow($gridRow, 'grant['.$module_id.']', isset($groupModules[$mdl['id']]));
}

// Return output
return $pageContent->getReport();
//#section_end#
?>