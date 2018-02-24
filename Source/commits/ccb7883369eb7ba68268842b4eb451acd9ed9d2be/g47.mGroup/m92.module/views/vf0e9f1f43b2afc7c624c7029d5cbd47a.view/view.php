<?php
//#section#[header]
// Module Declaration
$moduleID = 92;

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
importer::import("DEV", "Projects");
importer::import("DEV", "Resources");
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Model\modules\module;
use \SYS\Comm\db\dbConnection;
use \ESS\Environment\url;
use \API\Literals\moduleLiteral;
use \DEV\Resources\paths;
use \UI\Modules\MContent;
use \DEV\Projects\projectLibrary;

// Create Module Page
$pageContent = new MContent($moduleID);

// Get action factory
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "appHolder");


// Get app container
//$appContainer = HTML::select(".appCenterUserHome .appHolder")->item(0);
$appContainer = $pageContent->get();

// Get boss apps
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_appcenter_apps");
$result = $dbc->execute($q);
$apps = $dbc->fetch($result, TRUE);

$rowMaxObjects = 4;
$count = 0;
foreach ($apps as $app)
{
	$count++;
	$index = $count % $rowMaxObjects;
	$rowIndex = ($count - $index) * $rowMaxObjects;
	
	if($index == 1)
	{
		// Create new row
		$row = DOM::create('div', '', '', 'row');
		DOM::append($appContainer, $row);
	}
	
	// Create app box weblink
	/*
	$url = url::resolve("apps", "/info.php");
	$params = array();
	if(!empty($app['name']))
		$params['name'] = $app['name'];
	else
		$params['id'] = $app['id'];
	$url = url::get($url, $params);
	$appBox = $pageContent->getWebLink($url, "", "_self");
	HTML::addClass($appBox, "appTile");
	*/
	
	
	$appBox = DOM::create('div', '', '', 'appTile');	
		$appBoxUpper = DOM::create('div', '', '', 'upper');
			$appIcoWrapper = DOM::create("div", "", "", "appIcoWrapper"); 
				// Application Ico
				$appIco = DOM::create("div", "", "", "appIco"); 
					// Get application icon
					$appIcon = projectLibrary::getPublishedPath($app['id'], $app['version'])."/resources/.assets/icon.png";
					// If file not exists, try old icon
					if (!file_exists(systemRoot.$appIcon))
						$appIcon = projectLibrary::getPublishedPath($app['id'], $app['version'])."/resources/ico.png";
					if (file_exists(systemRoot.$appIcon))
					{
						$appTileIcon = str_replace(paths::getPublishedPath(), "", $appIcon);
						$appTileIcon = url::resolve("lib", $appTileIcon);
						
						// Create icon img
						$img = DOM::create("img");
						DOM::attr($img, "src", $appTileIcon);
						DOM::append($appIco, $img);
					} 
				DOM::append($appIcoWrapper, $appIco);
			DOM::append($appBoxUpper, $appIcoWrapper);
			
			$appInfoWrapper = DOM::create("div", "", "", "appInfoWrapper");
			
				// Get Application Title 'Info' Link
				if (!empty($app['name']))
					$url = url::resolve("apps", "/".$app['name']);
				else
				{
					$params = array(); 
					$params['id'] = $app['id'];
					$url = url::resolve("apps", "/index.php", $params);
				}
				$title = DOM::create('span', $app['title']);
				$appTitle = $pageContent->getWebLink($url, $title, "_self");
				HTML::addClass($appTitle, "appTitle");
				DOM::append($appInfoWrapper, $appTitle);
				
				// Owner Team
				$appDevs = DOM::create('div', '', '', 'appDevs');
					$inner = DOM::create("span", "Team Name", "", "");
					DOM::append($appDevs, $inner);			
				DOM::append($appInfoWrapper, $appDevs);
				
			DOM::append($appBoxUpper, $appInfoWrapper);				
		
			/*
			// Play Control
			$runButton = DOM::create("div", "Run Me", "", "");
			DOM::append($appBoxUpper, $runButton);	
			*/
		DOM::append($appBox, $appBoxUpper);
		
		
		/*
		$appBoxMiddle = DOM::create('div', '', '', 'middle');
			$appDescription = DOM::create("div", $app['description'], "", "appDescription");
			DOM::append($appBoxMiddle, $appDescription);		
		DOM::append($appBox, $appBoxMiddle);
		*/
		
		$appBoxLower = DOM::create('div', '', '', 'lower');
			$inner = DOM::create("div", "", "", "inner");
			
			// Get Application Title 'Info' Link
			if (!empty($app['name']))
				$url = url::resolve("apps", "/".$app['name']."/play");
			else
			{
				$params = array(); 
				$params['id'] = $app['id'];
				$url = url::resolve("apps", "/player.php", $params);
			}
			$text = moduleLiteral::get($moduleID, "lbl_playApp");
			$appPlay = $pageContent->getWebLink($url, $text, "_self"); 
			HTML::addClass($appPlay, "playBtn");
			DOM::append($inner, $appPlay);
			
			
			DOM::append($appBoxLower, $inner);			
		DOM::append($appBox, $appBoxLower);
	DOM::append($row, $appBox);
	
	
	// Create app box weblink
	/*
	$url = url::resolve("apps", "/application.php");
	$params = array();
	$params['id'] = $app['id'];
	$url = url::get($url, $params);
	
	DOM::attr($appBox, "data-target", $url);
	*/
}	










// Return output
return $pageContent->getReport();
//#section_end#
?>