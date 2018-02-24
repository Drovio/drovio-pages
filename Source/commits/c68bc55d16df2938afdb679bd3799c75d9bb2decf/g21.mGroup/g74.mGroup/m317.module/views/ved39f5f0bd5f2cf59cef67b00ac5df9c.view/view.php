<?php
//#section#[header]
// Module Declaration
$moduleID = 317;

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

// Build the content
$content = new MContent($moduleID);
$content->build("", "myAccountKeys", TRUE);

// Get Account Keys
$akeys = accountKey::get();

// Get team and project keys
$teamKeys = array();
$projectKeys = array();
foreach ($akeys as $akey)
	if ($akey['type_id'] == accountKey::TEAM_KEY_TYPE)
		$teamKeys[] = $akey;
	else
		$projectKeys[] = $akey;
		
$list = HTML::select(".keylist")->item(0);
$teamList = HTML::select(".keylist .teamKeys")->item(0);
$projectList = HTML::select(".keylist .projectKeys")->item(0);
if (count($akeys) == 0)
	HTML::replace($list, NULL);
else
{
	// Remove nokey message
	$nokey = HTML::select("h4.nokeys")->item(0);
	HTML::replace($nokey, NULL);
	
	
	// Team keys
	if (count($teamKeys) > 0)
	{
		$headers = array();
		$headers[] = "ID";
		$headers[] = "Team";
		$headers[] = "Account Group";
		$headers[] = "Key";
		if (account::isAdmin())
			$headers[] = "Regenerate";
		$teamKeyList = getKeyList($teamKeys, $moduleID, $headers);
		DOM::append($teamList, $teamKeyList);
	}
	
	// Project Keys
	if (count($projectKeys) > 0)
	{
		$headers = array();
		$headers[] = "ID";
		$headers[] = "Project";
		$headers[] = "Account Group";
		$headers[] = "Key";
		if (account::isAdmin())
			$headers[] = "Regenerate";
		$projectKeyList = getKeyList($projectKeys, $moduleID, $headers);
		DOM::append($projectList, $projectKeyList);
	}
}

return $content->getReport();

function getKeyList($keyList, $moduleID, $headers)
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
		$row[] = $key['context'];
		$row[] = accountKey::getContextDescription($key['type_id'], $key['context']);
		$row[] = $key['groupName'];
		$class = "k".$key['userGroup_id'].$key['type_id'].$key['context'];
		$row[] = DOM::create("span", $key['akey'], "", $class);
		
		// Skip key regenerate form if managed account
		if (!account::isAdmin())
		{
			$gridList->insertRow($row);
			continue;
		}
			
		// Regenerate form
		$form = new simpleForm();
		$regenForm = $form->build("", FALSE)->engageModule($moduleID, "regenerateKey")->get();
		
		// Add hidden values
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