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
importer::import("API", "Security");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Geoloc\datetimer;
use \API\Model\modules\module;
use \API\Security\account;
use \UI\Modules\MContent;
use \UI\Presentation\dataGridList;

// Build the content
$content = new MContent($moduleID);
$content->build("", "myAccountKeys", TRUE);

// Get Account Keys
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_account_keys");
$attr = array();
$attr['aid'] = account::getAccountID();
$result = $dbc->execute($q, $attr);
$akeys = $dbc->fetch($result, TRUE);

$list = HTML::select(".keylist")->item(0);
if (count($akeys) == 0)
	HTML::replace($list, NULL);
else
{
	// Remove nokey message
	$nokey = HTML::select("h4.nokeys")->item(0);
	HTML::replace($nokey, NULL);
	
	// Create key grid list
	$gridList = new dataGridList();
	$keyList = $gridList->build()->get();
	DOM::append($list, $keyList);
	
	// Set headers
	$headers = array();
	$headers[] = "Group";
	$headers[] = "Type";
	$headers[] = "Context";
	$headers[] = "Key";
	$headers[] = "Date Created";
	$gridList->setHeaders($headers);
	
	// Add keys
	foreach ($akeys as $key)
	{
		$row = array();
		$row[] = $key['groupName'];
		$row[] = $key['type'];
		$row[] = $key['context'];
		$row[] = $key['akey'];
		$row[] = datetimer::live($key['time_created']);
		$gridList->insertRow($row);
	}
}

return $content->getReport();
//#section_end#
?>