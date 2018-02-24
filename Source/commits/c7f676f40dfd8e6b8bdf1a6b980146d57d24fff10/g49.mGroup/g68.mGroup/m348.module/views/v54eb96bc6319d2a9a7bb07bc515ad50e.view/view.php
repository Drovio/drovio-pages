<?php
//#section#[header]
// Module Declaration
$moduleID = 348;

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
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("DEV", "Projects");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \API\Profile\account;
use \API\Security\accountKey;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\dataGridList;
use \DEV\Projects\project;

// Build the content
$content = new MContent($moduleID);
$content->build("", "myAccountKeys", TRUE);

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];

// Get Account Keys
$akeys = accountKey::get();

// Get team and project keys
$teamKeys = array();
$projectKeys = array();
foreach ($akeys as $akey)
	if ($akey['project_id'] == $projectID)
		$projectKeys[] = $akey;
		
$projectList = HTML::select(".keylist.projectKeys")->item(0);

$headers = array();
$headers[] = "ID";
$headers[] = "Project";
$headers[] = "Account Group";
$headers[] = "Key";
if (account::isAdmin())
	$headers[] = "Regenerate";
$projectKeyList = getKeyList($projectKeys, $moduleID, $headers, $projectID);
DOM::append($projectList, $projectKeyList);

return $content->getReport();

function getKeyList($keyList, $moduleID, $headers, $projectID)
{
	// Create key grid list
	$gridList = new dataGridList();
	$gridList->build();
	
	// Set column ratios
	$ratios = array();
	$ratios[] = "0.05";
	$ratios[] = "0.2";
	$ratios[] = "0.2";
	$ratios[] = "0.35";
	$ratios[] = "0.2";
	$gridList->setColumnRatios($ratios);
	
	// Set headers
	$gridList->setHeaders($headers);
	
	// Add keys
	foreach ($keyList as $key)
	{
		$row = array();
		$row[] = $key['project_id'];
		$row[] = accountKey::getContextDescription(accountKey::PROJECT_KEY_TYPE, $key['project_id']);
		$row[] = $key['user_group_name'];
		$class = "k".$key['user_group_id'].accountKey::PROJECT_KEY_TYPE.$key['project_id'];
		$row[] = DOM::create("span", $key['akey'], "", $class);
		
		// Skip key regenerate form if managed account
		if (TRUE)//!account::isAdmin())
		{
			$gridList->insertRow($row);
			continue;
		}
			
		// Regenerate form
		$form = new simpleForm();
		$regenForm = $form->build("", FALSE)->engageModule($moduleID, "regenerateKey")->get();
		
		// Add hidden values
		$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "gid", $value = $key['userGroup_id'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "tid", $value = $key['type_id'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "ctx", $value = $key['context'], $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$btn = $form->getSubmitButton($title = "Regenerate", $id = "");
		$form->append($btn);
		
		$row[] = $regenForm;
		$gridList->insertRow($row);
	}
	
	return $gridList->get();
}
//#section_end#
?>