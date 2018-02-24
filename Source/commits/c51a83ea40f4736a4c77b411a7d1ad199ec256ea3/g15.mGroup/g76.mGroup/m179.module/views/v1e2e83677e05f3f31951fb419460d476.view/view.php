<?php
//#section#[header]
// Module Declaration
$moduleID = 179;

// Inner Module Codes
$innerModules = array();
$innerModules['rvAnalytics'] = 180;
$innerModules['rvSystemReach'] = 181;
$innerModules['rvTimeTraffic'] = 182;

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
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\special\datepicker;
use \UI\Modules\MPage;
use \UI\Navigation\sideMenu;
use \UI\Presentation\gridSplitter;

$HTMLModulePage = new MPage();
$HTMLModulePage->build($pageTitle, "", TRUE);
$actionFactory = $HTMLModulePage->getActionFactory();

$mainContent = HTML::select(".uiMainContent")->item(0);

$splitter = new gridSplitter();
$splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_LEFT, $closed = FALSE, "SideBar");
DOM::append($mainContent, $splitter->get());

$targetcontainer = 'pageContent';
$targetgroup = 'sideMenuTargetGroup';
$navgroup = 'sideMenuNavGroup';
$display = "none";


$sidebar  = DOM::create("div", "", "", "sidebar");
$splitter->appendToSide($sidebar);	

$sideMenu = new sideMenu();
$sideMenu->build('', 'Redback Reach');
DOM::append($sidebar, $sideMenu->get());

$item = $sideMenu->insertListItem($id = "", moduleLiteral::get($moduleID, "lbl_pageHits", FALSE), $selected = TRUE);
$sideMenu->addNavigation($item, 'rvAnalytics', $targetcontainer, $targetgroup, $navgroup, $display);
$item = $sideMenu->insertListItem($id = "", moduleLiteral::get($moduleID, "lbl_moduleTraffic", FALSE), $selected = FALSE);
$sideMenu->addNavigation($item, 'rvAnalytics_2', $targetcontainer, $targetgroup, $navgroup, $display);
$item = $sideMenu->insertListItem($id = "", moduleLiteral::get($moduleID, "lbl_pageRaw", FALSE), $selected = FALSE);
$sideMenu->addNavigation($item, 'pageHitsRaw', $targetcontainer, $targetgroup, $navgroup, $display);
$item = $sideMenu->insertListItem($id = "", moduleLiteral::get($moduleID, "lbl_moduleRaw", FALSE), $selected = FALSE);
$sideMenu->addNavigation($item, 'moduleHitsRaw', $targetcontainer, $targetgroup, $navgroup, $display);


$sideMenu = new sideMenu();
$sideMenu->build('', 'Redback Users'); 
DOM::append($sidebar, $sideMenu->get());
$item = $sideMenu->insertListItem($id = "", "User Stats", $selected = FALSE);
$sideMenu->addNavigation($item, 'userStats', $targetcontainer, $targetgroup, $navgroup, $display);

$id = 'rvAnalytics';
$contentPage = DOM::create('div', '', $id, 'contentPage');
$sideMenu->addNavigationSelector($contentPage, $targetgroup);
	$sForm = new simpleForm();
	$sForm->build($innerModules['rvAnalytics'], "structuredData", $controls = FALSE);	
	
	// Holder
	$holder = $sForm->getInput("hidden", "holder", '#'.$id.' > .dataPresentation');	
	$sForm->append($holder);
	
	$rangeSelector = DOM::create('div', '', '', 'rangeSelector');
	$sForm->append($rangeSelector);
	
	$datepickerWrapper = DOM::create('div', '', '', 'controlWrapper');
	DOM::append($rangeSelector, $datepickerWrapper);
	$datepicker = new datepicker();
	$label = moduleLiteral::get($moduleID, "lbl_from");	
	DOM::append($datepickerWrapper, $label);
	$colon = DOM::create('span', ':');
	DOM::append($datepickerWrapper, $colon);
	$datepicker->build('startDate_1');
	DOM::append($datepickerWrapper, $datepicker->get());
	
	$datepickerWrapper = DOM::create('div', '', '', 'controlWrapper');
	DOM::append($rangeSelector, $datepickerWrapper);
	$datepicker = new datepicker();
	$label = moduleLiteral::get($moduleID, "lbl_to");
	DOM::append($datepickerWrapper, $label);
	$colon = DOM::create('span', ':');
	DOM::append($datepickerWrapper, $colon);
	$datepicker->build('endDate_1');
	DOM::append($datepickerWrapper, $datepicker->get());
	
	// Form Buttons
	$title = DOM::create("span", "Display");
	$submit = $sForm->getSubmitButton($title, "");
	$sForm->append($submit);
	
	$filtersContainer = DOM::create('div', '', '', 'filtersWrapper');
	DOM::append($contentPage, $filtersContainer);
	$headerBar = DOM::create('div', '', '', 'headerBar');
		DOM::append($headerBar, moduleLiteral::get($moduleID, "lbl_pageHits"));
	DOM::append($filtersContainer, $headerBar);
	
	DOM::append($filtersContainer, $sForm->get());
	
	$dataPresentation = DOM::create('div', '', '', 'dataPresentation verticalScroll');
	//$content = DOM::create('span', 'no Data');
	//$content = $HTMLContent->getModuleContainer($moduleID, "structuredData", $attr = array(), $startup = TRUE, 'basicViewData');
	DOM::append($dataPresentation, $content);
	DOM::append($contentPage, $dataPresentation);
$splitter->appendToMain($contentPage);

$id = 'rvAnalytics_2';
$contentPage = DOM::create('div', '', $id, 'contentPage noDisplay');
$sideMenu->addNavigationSelector($contentPage, $targetgroup);
	$sForm = new simpleForm();
	$sForm->build($innerModules['rvAnalytics'], "structuredData", $controls = FALSE);	
	
	// Holder
	$holder = $sForm->getInput("hidden", "holder", '#'.$id.' > .dataPresentation');
	$sForm->append($holder);
	
	$rangeSelector = DOM::create('div', '', '', 'rangeSelector');
	$sForm->append($rangeSelector);
	
	$datepickerWrapper = DOM::create('div', '', '', 'controlWrapper');
	DOM::append($rangeSelector, $datepickerWrapper);
	$datepicker = new datepicker();
	$label = moduleLiteral::get($moduleID, "lbl_from");
	DOM::append($datepickerWrapper, $label);
	$colon = DOM::create('span', ':');
	DOM::append($datepickerWrapper, $colon);
	$datepicker->build('startDate_2');
	DOM::append($datepickerWrapper, $datepicker->get());
	
	$datepickerWrapper = DOM::create('div', '', '', 'controlWrapper');
	DOM::append($rangeSelector, $datepickerWrapper);
	$datepicker = new datepicker();
	$label = moduleLiteral::get($moduleID, "lbl_to");
	DOM::append($datepickerWrapper, $label);
	$colon = DOM::create('span', ':');
	DOM::append($datepickerWrapper, $colon);
	$datepicker->build('endDate_2');
	DOM::append($datepickerWrapper, $datepicker->get());
	
	// Form Buttons
	$title = DOM::create("span", "Display");
	$submit = $sForm->getSubmitButton($title, "");
	$sForm->append($submit);
	
	$filtersContainer = DOM::create('div', '', '', 'filtersWrapper');
	DOM::append($contentPage, $filtersContainer);
	$headerBar = DOM::create('div', '', '', 'headerBar');
		DOM::append($headerBar, moduleLiteral::get($moduleID, "lbl_moduleTraffic"));
	DOM::append($filtersContainer, $headerBar);
	DOM::append($filtersContainer, $sForm->get());
	
	$dataPresentation = DOM::create('div', '', 'basicViewDataContainer', 'dataPresentation verticalScroll');
	//$content = DOM::create('span', 'no Data');
	//$content = $HTMLContent->getModuleContainer($moduleID, "structuredData", $attr = array(), $startup = TRUE, 'basicViewData');
	DOM::append($dataPresentation, $content );
	DOM::append($contentPage, $dataPresentation);
$splitter->appendToMain($contentPage);

$id = 'pageHitsRaw';
$contentPage = DOM::create('div', '', $id, 'contentPage noDisplay');
$sideMenu->addNavigationSelector($contentPage, $targetgroup);
	$sForm = new simpleForm();
	$sForm->build($innerModules['rvAnalytics'], "pageHitsRaw", $controls = FALSE);	
	
	// Holder
	$holder = $sForm->getInput("hidden", "holder", '#'.$id.' > .dataPresentation');
	$sForm->append($holder);
	
	$rangeSelector = DOM::create('div', '', '', 'rangeSelector');
	$sForm->append($rangeSelector);
	
	$datepickerWrapper = DOM::create('div', '', '', 'controlWrapper');
	DOM::append($rangeSelector, $datepickerWrapper);
	$datepicker = new datepicker();
	$label = moduleLiteral::get($moduleID, "lbl_from");
	DOM::append($datepickerWrapper, $label);
	$colon = DOM::create('span', ':');
	DOM::append($datepickerWrapper, $colon);
	$datepicker->build('startDate_3');
	DOM::append($datepickerWrapper, $datepicker->get());
	
	$datepickerWrapper = DOM::create('div', '', '', 'controlWrapper');
	DOM::append($rangeSelector, $datepickerWrapper);
	$datepicker = new datepicker();
	$label = moduleLiteral::get($moduleID, "lbl_to");
	DOM::append($datepickerWrapper, $label);
	$colon = DOM::create('span', ':');
	DOM::append($datepickerWrapper, $colon);
	$datepicker->build('endDate_3');
	DOM::append($datepickerWrapper, $datepicker->get());
	
	// Form Buttons
	$title = DOM::create("span", "Display");
	$submit = $sForm->getSubmitButton($title, "");
	$sForm->append($submit);
	
	$filtersContainer = DOM::create('div', '', '', 'filtersWrapper');
	DOM::append($contentPage, $filtersContainer);
	$headerBar = DOM::create('div', '', '', 'headerBar');
		DOM::append($headerBar, moduleLiteral::get($moduleID, "lbl_pageRaw"));
	DOM::append($filtersContainer, $headerBar);
	DOM::append($filtersContainer, $sForm->get());
	
	$dataPresentation = DOM::create('div', '', 'basicViewDataContainer', 'dataPresentation verticalScroll');
	//$content = DOM::create('span', 'no Data');
	//$content = $HTMLContent->getModuleContainer($moduleID, "structuredData", $attr = array(), $startup = TRUE, 'basicViewData');
	DOM::append($dataPresentation, $content );
	DOM::append($contentPage, $dataPresentation);
$splitter->appendToMain($contentPage); 

$id = 'moduleHitsRaw';
$contentPage = DOM::create('div', '', $id, 'contentPage noDisplay');
$sideMenu->addNavigationSelector($contentPage, $targetgroup);
	$sForm = new simpleForm();
	$sForm->build($innerModules['rvAnalytics'], "moduleHitsRaw", $controls = FALSE);	
	
	// Holder
	$holder = $sForm->getInput("hidden", "holder", '#'.$id.' > .dataPresentation');
	$sForm->append($holder);
	
	$rangeSelector = DOM::create('div', '', '', 'rangeSelector');
	$sForm->append($rangeSelector);
	
	$datepickerWrapper = DOM::create('div', '', '', 'controlWrapper');
	DOM::append($rangeSelector, $datepickerWrapper);
	$datepicker = new datepicker();
	$label = moduleLiteral::get($moduleID, "lbl_from");
	DOM::append($datepickerWrapper, $label);
	$colon = DOM::create('span', ':');
	DOM::append($datepickerWrapper, $colon);
	$datepicker->build('startDate_4');
	DOM::append($datepickerWrapper, $datepicker->get());
	
	$datepickerWrapper = DOM::create('div', '', '', 'controlWrapper');
	DOM::append($rangeSelector, $datepickerWrapper);
	$datepicker = new datepicker();
	$label = moduleLiteral::get($moduleID, "lbl_to");
	DOM::append($datepickerWrapper, $label);
	$colon = DOM::create('span', ':');
	DOM::append($datepickerWrapper, $colon);
	$datepicker->build('endDate_4');
	DOM::append($datepickerWrapper, $datepicker->get());
	
	// Form Buttons
	$title = DOM::create("span", "Display");
	$submit = $sForm->getSubmitButton($title, "");
	$sForm->append($submit);
	
	$filtersContainer = DOM::create('div', '', '', 'filtersWrapper');
	DOM::append($contentPage, $filtersContainer);
	$headerBar = DOM::create('div', '', '', 'headerBar');
		DOM::append($headerBar, moduleLiteral::get($moduleID, "lbl_moduleRaw"));
	DOM::append($filtersContainer, $headerBar);
	DOM::append($filtersContainer, $sForm->get());
	
	$dataPresentation = DOM::create('div', '', 'basicViewDataContainer', 'dataPresentation verticalScroll');
	//$content = DOM::create('span', 'no Data');
	//$content = $HTMLContent->getModuleContainer($moduleID, "structuredData", $attr = array(), $startup = TRUE, 'basicViewData');
	DOM::append($dataPresentation, $content );
	DOM::append($contentPage, $dataPresentation);
$splitter->appendToMain($contentPage);

$id = 'userStats';
$contentPage = $HTMLModulePage->getModuleContainer($innerModules['rvSystemReach'], "", $attr = array(), $startup = TRUE, $id);
DOM::appendAttr($contentPage, 'class', 'contentPage noDisplay');
$sideMenu->addNavigationSelector($contentPage, $targetgroup);
$splitter->appendToMain($contentPage);

$id = 'rvTimeTraffic';
$contentPage = $HTMLModulePage->getModuleContainer($innerModules['rvTimeTraffic'], "", $attr = array(), $startup = TRUE, $id);
DOM::appendAttr($contentPage, 'class', 'contentPage noDisplay');
$sideMenu->addNavigationSelector($contentPage, $targetgroup);
$splitter->appendToMain($contentPage);


// Return the report
return $HTMLModulePage->getReport();
//#section_end#
?>