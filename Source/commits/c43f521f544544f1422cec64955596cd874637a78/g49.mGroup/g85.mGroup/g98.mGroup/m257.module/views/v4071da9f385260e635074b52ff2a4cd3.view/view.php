<?php
//#section#[header]
// Module Declaration
$moduleID = 257;

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
importer::import("API", "Profile");
importer::import("DEV", "Modules");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\sql\dbQuery;
use \API\Model\modules\mGroup;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \UI\Modules\MContent;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\dataGridList;
use \DEV\Modules\modulesProject;

// Get reference account id
$accountID = engine::getVar('aid');
$groups = mGroup::getAllGroups();

$groupsInfo = array();
$groupsPost = array();
foreach ($groups as $groupInfo)
	$groupsInfo[$groupInfo['id']]['description'] = $groupInfo['description'];

// get user's module groups with developer privileges
$dbc = new dbConnection();
$dbq = new dbQuery("677677266", "security.privileges.developer");
$attr = array();
$attr['aid'] = $accountID;
$groups = $dbc->execute($dbq, $attr);

while ($row = $dbc->fetch($groups))
{
	$groupsInfo[$row['id']]['developer'] = TRUE;
	$groupsPost['developer'][$row['id']] = 'on';
}

// get user's module groups with master privileges
$dbq = new dbQuery("785059449", "security.privileges.developer");

$attr = array();
$attr['aid'] = $accountID;
$groups = $dbc->execute($dbq, $attr);

while ($row = $dbc->fetch($groups))
{
	$groupsInfo[$row['id']]['master'] = TRUE;
	$groupsPost['master'][$row['id']] = 'on';
}

// get user's module groups with tester privileges
$dbq = new dbQuery("1253567185", "security.privileges.tester");

$attr = array();
$attr['aid'] = $accountID;
$groups = $dbc->execute($dbq, $attr);

while ($row = $dbc->fetch($groups))
{
	$groupsInfo[$row['id']]['tester'] = TRUE;
	$groupsPost['tester'][$row['id']] = 'on';
}

if (engine::isPost())
{
	$types = array();
	$types['developer']['grant'] = "184651428";
	$types['developer']['revoke'] = "1513206737";
	$types['developer']['qdomain'] = "security.privileges.developer";
	$types['master']['grant'] = "638584576";
	$types['master']['revoke'] = "1114590665";
	$types['master']['qdomain'] = "security.privileges.developer";
	$types['tester']['grant'] = "1622807987";
	$types['tester']['revoke'] = "1289382855";
	$types['tester']['qdomain'] = "security.privileges.tester";
	
	foreach ($types as $t => $info)
	{
		if (!isset($groupsPost[$t]))
			$groupsPost[$t] = array();
		if (!isset($_POST['privs'][$t]))
			$_POST['privs'][$t] = array();
	}
	
	$grant = array();
	$revoke = array();
	// Check what needs to be updated
	foreach ($groupsPost as $type => $privileges)
	{	
		$grant[$type] = array_diff_assoc($_POST['privs'][$type], $privileges);
		$revoke[$type] = array_diff_assoc($privileges, $_POST['privs'][$type]);
	}
	
	// For developer, master, and tester
	foreach ($types as $type => $qinfo)
	{			
		foreach ($revoke[$type] as $gid => $value)
		{
			// Revoke access
			$dbq = new dbQuery($qinfo['revoke'], $qinfo['qdomain']);
			
			$attr = array();
			$attr['aid'] = $accountID;
			$attr['gid'] = $gid;
			$result = $dbc->execute($dbq, $attr);
			
			if (!$result)
			{
				//error exists
				$formNotification = new formNotification();
				$formNotification->build($type = formNotification::ERROR);
				
				// Description
				$formNotification->appendCustomMessage("Revoke access Error");
				
				return $formNotification->getReport(FALSE);		
			}
		}
		
		foreach ($grant[$type] as $gid => $value)
		{	
			// Grant access
			$dbq = new dbQuery($qinfo['grant'], $qinfo['qdomain']);
			
			$attr = array();
			$attr['aid'] = $accountID;
			$attr['gid'] = $gid;
			$result = $dbc->execute($dbq, $attr);
			
			// Check For error In Query execution
			if (!$result)
			{
				//error exists
				$formNotification = new formNotification();
				$formNotification->build($type = formNotification::ERROR);
				
				// Description
				$formNotification->appendCustomMessage("Grant access Error");
				
				return $formNotification->getReport(FALSE);		
			}
		}
	}
	
	$successNotification = new formNotification();
	$successNotification->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Description
	$message= $successNotification->getMessage("success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport(FALSE);
}

// Create Module Page
$pageContent = new MContent($moduleID); 
$pageContent->build("", "privilegesInfoContainer");

$form = new simpleForm();
$devPrivilegesForm = $form->build("", FALSE)->engageModule($moduleID, "privilegesInfo")->get();
$pageContent->append($devPrivilegesForm);

// Project ID
$input = $form->getInput($type = "hidden", $name = "id", $value = modulesProject::PROJECT_ID, $class = "", $autofocus = FALSE);
$form->append($input);
 
// Account ID
$input = $form->getInput($type = "hidden", $name = "aid", $value = $accountID, $class = "", $autofocus = FALSE);
$form->append($input);

// Add submit button
$title = moduleLiteral::get($moduleID, "lbl_savePrivileges");
$button = $form->getSubmitButton($title, "btn_update");
$form->append($button);

// Form Header
$accountInfo = account::info($accountID);
$attr = array();
$attr['title'] = $accountInfo['accountTitle'];
$title = moduleLiteral::get($moduleID, "lbl_mdlGroups", $attr);
$header = DOM::create('h3', $title, "", "hd");
$form->append($header);

$formContentWrapper = DOM::create("div", "", "", "devPrivilegesFormContents");
$form->append($formContentWrapper);

// Create grid
$dtGridList = new dataGridList();
$glist = $dtGridList->build("", FALSE)->get();
DOM::append($formContentWrapper, $glist);

$ratios = array();
$ratios[] = 0.1;
$ratios[] = 0.15;
$ratios[] = 0.15;
$ratios[] = 0.1;
$ratios[] = 0.5;
$dtGridList->setColumnRatios($ratios);

$headers = array();
$headers[] = "GroupID";
$headers[] = "Developer";
$headers[] = "MDeveloper";
$headers[] = "Tester";
$headers[] = "Group";
$dtGridList->setHeaders($headers);

// Present Info
foreach ($groupsInfo as $groupID => $groupInfo)
{
	$gridRow = array();
	
	// Group ID
	$gridRow[] = DOM::create("span",  "".$groupID);
	
	// Group Developer
	$developer = $form->getInput($type = "checkbox", $name = 'privs[developer]['.$groupID.']', $value = "", $class = "", $autofocus = FALSE);
	if (isset($groupInfo['developer']))
		DOM::attr($developer, "checked", "checked");
	$gridRow[] = $developer;
	
	
	// Group Master Developer	
	$master = $form->getInput($type = "checkbox", $name = 'privs[master]['.$groupID.']', $value = "", $class = "", $autofocus = FALSE);
	if (isset($groupInfo['master']))
		DOM::attr($master, "checked", "checked");
	$gridRow[] = $master;
	
	
	// Group Tester
	$tester = $form->getInput($type = "checkbox", $name = 'privs[tester]['.$groupID.']', $value = "", $class = "", $autofocus = FALSE);
	if (isset($groupInfo['tester']))
		DOM::attr($tester, "checked", "checked");
	$gridRow[] = $tester;
	
	// Group Trail
	$gridRow[] = DOM::create("span", mGroup::getTrail($groupID));
	
	// Insert row
	$dtGridList->insertRow($gridRow);
}
// Return output
return $pageContent->getReport();
//#section_end#
?>