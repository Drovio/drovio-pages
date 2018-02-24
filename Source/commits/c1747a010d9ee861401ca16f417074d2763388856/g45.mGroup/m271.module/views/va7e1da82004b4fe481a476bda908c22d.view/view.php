<?php
//#section#[header]
// Module Declaration
$moduleID = 271;

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
importer::import("API", "Profile");
importer::import("API", "Literals");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \ESS\Environment\url;
use \API\Model\modules\module;
use \API\Profile\team;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\popups\popup;
use \DEV\Projects\projectLibrary;
use \DEV\Resources\paths;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "appDetailsContainer", TRUE);

$appID = $_GET['id'];
$appVersion = $_GET['version'];

// Get application info
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_app_info");
$attr = array();
$attr['id'] = $appID;
$attr['version'] = $appVersion;
$result = $dbc->execute($q, $attr);
$appInfo = $dbc->fetch($result);


// Application Ico
$appIco = HTML::select(".appDetails .header .ico")->item(0);
$appIcon = projectLibrary::getPublishedPath($appID, $appVersion)."/resources/.assets/ico.png";
// If file not exists, try old icon
if (!file_exists(systemRoot.$appIcon))
	$appIcon = projectLibrary::getPublishedPath($appID, $appVersion)."/resources/ico.png";
if (file_exists(systemRoot.$appIcon))
{
	$appTileIcon = str_replace(paths::getPublishedPath(), "", $appIcon);
	$appTileIcon = url::resolve("lib", $appTileIcon);
	
	// Create icon img
	$img = DOM::create("img");
	DOM::attr($img, "src", $appTileIcon);
	DOM::append($appIco, $img);
}


// Application title
$appTitle = HTML::select(".appDetails .header .title")->item(0);
HTML::innerHTML($appTitle, $appInfo['title']);

// Application version
$appVer = HTML::select(".appDetails .header .version")->item(0);
$version = DOM::create("span", $appInfo['version']);
HTML::append($appVer, $version);


// Application description
$appDesc = HTML::select(".appDetails .body .app_desc")->item(0);
HTML::innerHTML($appDesc, $appInfo['description']);

// Application changelog
$appChlog = HTML::select(".appDetails .body .v_changelog")->item(0);
HTML::innerHTML($appChlog, $appInfo['changelog']);



// Check if team has this application
$q = module::getQuery($moduleID, "get_team_app_version");
$attr = array();
$attr['app_id'] = $appID;
$attr['tid'] = team::getTeamID();
$result = $dbc->execute($q, $attr);
$appTeamVersion = $dbc->fetch($result);

$appStatus = HTML::select(".appDetails .header .appStatus")->item(0);
if (empty($appTeamVersion))
{
	$form = new simpleForm();
	$formGetter = $form->build("", FALSE)->engageModule($moduleID, "buyApp")->get();
	DOM::append($appStatus, $formGetter);
	
	// Set application id and version
	$input = $form->getInput($type = "hidden", $name = "id", $value = $appID, $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	$input = $form->getInput($type = "hidden", $name = "version", $value = $appVersion, $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
	
	// Submit button
	$title = moduleLiteral::get($moduleID, "lbl_getApp");
	$btn = $form->getSubmitButton($title, $id = "");
	$form->append($btn);
}
else
{
	// Check if application is the same version
	if (version_compare($appTeamVersion['version'], $appVersion, "=="))
	{
		$title = moduleLiteral::get($moduleID, "lbl_appToDate");
		$appUpdated = DOM::create("div", $title, "", "updated");
		DOM::append($appStatus, $appUpdated);
		
		$ico = DOM::create("span", "", "", "ico");
		DOM::prepend($appUpdated, $ico);
	}
	else
	{
		$form = new simpleForm();
		$formGetter = $form->build("", FALSE)->engageModule($moduleID, "updateApp")->get();
		DOM::append($appStatus, $formGetter);
		
		// Set application id and version
		$input = $form->getInput($type = "hidden", $name = "id", $value = $appID, $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "version", $value = $appVersion, $class = "", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		// Submit button
		$title = moduleLiteral::get($moduleID, "lbl_updateApp");
		$btn = $form->getSubmitButton($title, $id = "");
		$form->append($btn);
	}
}


// Year on footer
$footerY = HTML::select(".appDetails .footer .year")->item(0);
HTML::innerHTML($footerY, date("Y", time()));


// Create popup
$pp = new popup();
$pp->position("user");
$pp->background(TRUE);
$pp->type("persistent");

$pp->build($pageContent->get());
return $pp->getReport();
//#section_end#
?>