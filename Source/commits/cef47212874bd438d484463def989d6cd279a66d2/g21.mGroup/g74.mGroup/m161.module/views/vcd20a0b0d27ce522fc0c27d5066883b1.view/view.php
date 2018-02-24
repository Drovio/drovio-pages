<?php
//#section#[header]
// Module Declaration
$moduleID = 161;

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
importer::import("API", "Geoloc");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \API\Geoloc\datetimer;
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
	if ($akey['type_id'] == 1)
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
		$teamKeyList = getKeyList($teamKeys, $moduleID);
		DOM::append($teamList, $teamKeyList);
	}
	
	// Project Keys
	if (count($projectKeys) > 0)
	{
		$projectKeyList = getKeyList($projectKeys, $moduleID);
		DOM::append($projectList, $projectKeyList);
	}
}

return $content->getReport();

function getKeyList($keyList, $moduleID)
{
	// Create key grid list
	$gridList = new dataGridList();
	$gridList->build();
	
	// Set column ratios
	$ratios = array();
	$ratios[] = "0.2";
	$ratios[] = "0.2";
	$ratios[] = "0.4";
	$ratios[] = "0.2";
	$gridList->setColumnRatios($ratios);
	
	// Set headers
	$headers = array();
	$headers[] = "Context";
	$headers[] = "Group";
	$headers[] = "Key";
	$headers[] = "Regenerate";
	$gridList->setHeaders($headers);
	
	// Add keys
	foreach ($keyList as $key)
	{
		$row = array();
		$row[] = accountKey::getContextDescription($key['type_id'], $key['context']);
		$row[] = $key['groupName'];
		$class = "k".$key['userGroup_id'].$key['type_id'].$key['context'];
		$row[] = DOM::create("span", $key['akey'], "", $class);
		
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