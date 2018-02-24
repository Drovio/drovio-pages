<?php
//#section#[header]
// Module Declaration
$moduleID = 158;

// Inner Module Codes
$innerModules = array();
$innerModules['coreSDK'] = 56;
$innerModules['coreAjax'] = 95;
$innerModules['coreSQL'] = 49;
$innerModules['modulesEditor'] = 64;
$innerModules['WELEditor'] = 121;
$innerModules['AELEditor'] = 125;
$innerModules['resourcesManager'] = 124;
$innerModules['docs'] = 170;

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Resources");
importer::import("API", "Resources");
importer::import("API", "Resources");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("API", "Comm");
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\environment\url;
use \API\Comm\database\connections\interDbConnection;
use \API\Resources\literals\moduleLiteral;
use \API\Model\units\sql\dbQuery;
use \UI\Html\HTMLModulePage;
use \UI\Presentation\layoutContainer;
use \UI\Presentation\gridView;

// Create Module Page
$page = new HTMLModulePage("OneColumnCentered");
$actionFactory = $page->getActionFactory();

// Build the module
$pageTitle = moduleLiteral::get($moduleID, "lbl_pageTitle", FALSE);
$page->build($pageTitle, "developerHomePage");


// Get Core Projects
$dbc = new interDbConnection();
$dbq = new dbQuery("958112878", "developer");

$attr = array();
$attr['category'] = 1;
$result = $dbc->execute($dbq, $attr);

$projectActions = array();
$projectActions[2] = $innerModules['modulesEditor'];
$projectActions[3] = $innerModules['WELEditor'];
$projectActions[4] = $innerModules['AELEditor'];

$subActions = array();
$subActions[1] = $innerModules['coreSDK'];
$subActions[2] = $innerModules['coreAjax'];
$subActions[3] = $innerModules['coreSQL'];

while ($project = $dbc->fetch($result))
{
	// Create project box
	$projectBox = DOM::create("div", "", "", "projectBox");
	$page->appendToSection("mainContent", $projectBox);
	
	// App Controls Container
	$boxControls = DOM::create("div", "", "", "boxControls");
	DOM::append($projectBox, $boxControls);
	
	// Edit Control
	if (!empty($project['editorPath']))
	{
		$control = DOM::create("a", "", "", "boxCtrl edit");
		$url = url::resolve($project['editorSub'], $project['editorPath']);
		DOM::attr($control, "href", $url);
		DOM::attr($control, "target", "_self");
		DOM::append($boxControls, $control);
		
		// Set module action
		$actionFactory->setModuleAction($control, $projectActions[$project['id']]);
	}
	
	// VCS Control
	$control = DOM::create("a", "", "", "boxCtrl vcs");
	$url = url::resolve("admin", "/developer/vcs.php?id=".$project['id']);
	DOM::attr($control, "href", $url);
	DOM::attr($control, "target", "_self");
	DOM::append($boxControls, $control);
	
	// Application title
	$boxTitle= DOM::create("h3", $project['title'], "", "boxTitle");
	DOM::append($projectBox, $boxTitle);
	
	// Application description
	$boxDesc = DOM::create("p", $project['description'], "", "boxDesc");
	DOM::append($projectBox, $boxDesc);
	
	
	// Get sub projects
	$dbq = new dbQuery("1907515440", "developer");
	$attr = array();
	$attr['project'] = $project['id'];
	$subResult = $dbc->execute($dbq, $attr);
	while ($projectSub = $dbc->fetch($subResult))
	{
		// Create project box
		$subBox = DOM::create("div", "", "", "subBox");
		DOM::append($projectBox, $subBox);
		
		// App Controls Container
		$subControls = DOM::create("div", "", "", "subControls");
		DOM::append($subBox, $subControls);
		
		// Edit Control
		$control = DOM::create("a", "", "", "boxCtrl edit");
		$url = url::resolve($projectSub['editorSub'], $projectSub['editorPath']);
		DOM::attr($control, "href", $url);
		DOM::attr($control, "target", "_self");
		DOM::append($subControls, $control);
		
		$actionFactory->setModuleAction($control, $subActions[$projectSub['id']]);
		
		// Application title
		$subTitle = DOM::create("h3", $projectSub['title'], "", "subTitle");
		DOM::append($subBox, $subTitle);
	}
	
	
}

// Extra links
$title = moduleLiteral::get($moduleID, "lbl_resourceManager");
$weblink = DOM::create("a", $title, "", "extra");
$url = url::resolve("admin", "/developer/resources/");
DOM::attr($weblink, "href", $url);
DOM::attr($weblink, "target", "_self");
$actionFactory->setModuleAction($weblink, $innerModules['resourcesManager']);

$extra = DOM::create("h4", $weblink);
$page->appendToSection("mainContent", $extra);


$title = moduleLiteral::get($moduleID, "lbl_adminDevDocs");
$weblink = DOM::create("a", $title, "", "extra");
$url = url::resolve("admin", "/developer/docs/");
DOM::attr($weblink, "href", $url);
DOM::attr($weblink, "target", "_self");
$actionFactory->setModuleAction($weblink, $innerModules['docs']);

$extra = DOM::create("h4", $weblink);
$page->appendToSection("mainContent", $extra);


/*

//_____ Resource Manager
$title = moduleLiteral::get($moduleID, "lbl_resourceManager");
$content = weblink::get(url::resolve("admin", "/developer/resources/"), "_self", $title);
$gridView->append(1, 3, $content);

//_____ Admin Developer Docs
$title = moduleLiteral::get($moduleID, "lbl_adminDevDocs");
$content = weblink::get(url::resolve("admin", "/developer/docs/"), "_self", $title);
$gridView->append(2, 0, $content);*/

// Return output
return $page->getReport();
//#section_end#
?>