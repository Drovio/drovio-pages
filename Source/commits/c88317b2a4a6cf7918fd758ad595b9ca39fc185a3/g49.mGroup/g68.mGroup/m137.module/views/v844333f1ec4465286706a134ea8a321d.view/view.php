<?php
//#section#[header]
// Module Declaration
$moduleID = 137;

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
importer::import("API", "Resources");
importer::import("DEV", "Apps");
importer::import("ESS", "Environment");
importer::import("ESS", "Protocol");
importer::import("UI", "Apps");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \ESS\Protocol\BootLoader;
use \ESS\Protocol\loaders\AppLoader;
use \ESS\Protocol\reports\HTMLServerReport;
use \ESS\Environment\url;
use \UI\Modules\MContent;
use \UI\Apps\APPContent;
use \DEV\Apps\test\appTester;
use \DEV\Apps\application;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Get application id
$appID = engine::getVar('id');
$appName = engine::getVar('name');
$app = new application($appID, $appName);
$appID = $app->getID();

// Build the module for a valid application
$applicationContainer = $pageContent->build("applicationContainer", "applicationTester")->get();

// Activate tester mode for application
appTester::setPublisherLock(FALSE);

// Get initial application view
$appOutput = AppLoader::load($appID);
$output = json_decode($appOutput, TRUE);

// Remove application content container
$appContentContainer = HTML::select(".".APPContent::CONTAINER_CLASS)->item(0);
DOM::replace($appContentContainer, NULL);

// Fetch body content
foreach ($output['body'] as $body)
{
	$type = $body['type'];
	$context = $body['context'];
	switch ($type)
	{
		case HTMLServerReport::CONTENT_ACTION:
			$pageHelper = DOM::find("pageHelper");
			$actionContainer = DOM::create("span", "", "", "actionContainer");
			DOM::data($actionContainer, "action", $body['context']);
			DOM::append($pageHelper, $actionContainer);
			
			// Break
			break;
		case HTMLServerReport::CONTENT_DATA:
		case HTMLServerReport::CONTENT_HTML:
			// Get method and holder
			$method = $body['method'];
			$holder = $body['holder'];
			
			// Get holder and append context
			$holder = (empty($holder) ? APPContent::HOLDER : $holder);
			$holderElement = HTML::select($holder)->item(0);
			
			// Select method of append
			switch ($method)
			{
				case HTMLServerReport::APPEND_METHOD:
					$oldInnerHTML = DOM::innerHTML($holderElement);
					$newInnerHTML = $oldInnerHTML.$context;
					DOM::innerHTML($holderElement, $newInnerHTML);
					break;
				case HTMLServerReport::REPLACE_METHOD:
					DOM::innerHTML($holderElement, $context);
			}
	}
}


// Get application resources
$params = array();
$params['id'] = $appID;
$cssUrl = BootLoader::getTesterResourceUrl("/ajax/apps/css.php", "Application", $appID);
$jsUrl = BootLoader::getTesterResourceUrl("/ajax/apps/js.php", "Application", $appID);

// Add Header
$header = BootLoader::getResourceArray(4, "ApplicationTester", $appID, $cssUrl, $jsUrl, $tester = TRUE);
$pageContent->addResourceHeader($header['id'], $header);

// Return output
return $pageContent->getReport(APPContent::HOLDER);
//#section_end#
?>