<?php
//#section#[header]
// Module Declaration
$moduleID = 284;

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
importer::import("API", "Geoloc");
importer::import("API", "Model");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "Version");
importer::import("DEV", "Websites");
//#section_end#
//#section#[code]
use \API\Geoloc\datetimer;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \DEV\Version\vcs;
use \DEV\Websites\website;
use \DEV\Websites\wsServer;
use \API\Model\modules\module;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;
use \UI\Presentation\frames\windowFrame;
use \UI\Presentation\dataGridList;
use \UI\Presentation\togglers\expander;

// Get website id
$websiteID = $_REQUEST['id'];
if ($_SERVER['REQUEST_METHOD'] == "POST")
{	
	// Get the user input that configures the publish proccess
	// Init (and return) the 'repoManager' modules
	// To initiate in turn the repository commit and release proccess
	
	// Create Module Page
	$pageContent = new MContent($moduleID);
	$actionFactory = $pageContent->getActionFactory();
	
	// Build the module content
	$pageContent->build("", "uc");
	
	// Before Continuing Check for parameters validity
	
	// Get the server settings
	$server = new wsServer($websiteId, $serverId);
	$info = $server->info();
	
	if($info['connection'] != wsServer::CON_TYPE_FTP)
	{
		// Connection Protocol Not Supported		
	}
	
	
	
	// Since we are going to make a loadView call
	// Reset the $_SERVER['REQUEST_METHOD'], in order 
	// for the called module to load correctly
	// TODO 'do not mess with$_SERVER'
	// This is a dirthy approach, try using global vars	
	$_SERVER['REQUEST_METHOD'] = "GET";
	$hw = module::loadView($moduleID, 'repoManager');
	$pageContent->append($hw);
 	
	// Add Action	
	$pageContent->addReportAction('website.publish.commit');
	
	// Return output
	return $pageContent->getReport('.sequenceHolder');	
}




// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "wsPublisherDialog", TRUE);

// Build frame
$frame = new windowFrame();
$title = moduleLiteral::get($moduleID, "title");
$frame->build($title, "wsPublisherFrame");

// Build Form
$form = new simpleForm();
$form->build()->engageModule($moduleID, "websitePublisher");
$formHolder = HTML::select('.section.formHolder')->item(0);
DOM::append($formHolder, $form->get());

// Set website id
$input = $form->getInput("hidden", "id", $websiteID, "", TRUE, TRUE);
$form->append($input);

// Select Server
$wsServer = new wsServer($websiteID);
$servers = $wsServer->getServerList();
$resource = array();
foreach ($servers as $id => $srv)
	$resource[$id] = $srv['name'];
	
$title = moduleLiteral::get($moduleID, 'lbl_selectServer');
$input = $form->getResourceSelect("srvID", FALSE, "", $resource, "");
$form->insertRow($title, $input, TRUE, "");

// Relase Title
$title = moduleLiteral::get($moduleID, "lbl_releaseTitle");
$input = $form->getTextArea("releaseTitle", "", "", FALSE);
$form->insertRow($title, $input, TRUE);

// Release Summary 
$title = moduleLiteral::get($moduleID, "lbl_releaseSummary");
$input = $form->getTextArea("releaseSummary", "", "", FALSE);
$form->insertRow($title, $input, TRUE);

// Advance options
$expander = new expander();
$form->append($expander->build()->get());

$advancePrompt = DOM::create('div', '', '', 'miniatureTitle');
//$text = moduleLiteral::get($moduleID, "lbl_advancePrompt");
DOM::append($advancePrompt, $text);

$expander->appendToMiniature($advancePrompt);
$advanceOptions = DOM::create('div', '', '', 'advanceOptions');
$expander->appendToExpansion($advanceOptions);

// Some header text
$advanceHd = DOM::create('div', '', '', 'header');
	$text = moduleLiteral::get($moduleID, "lbl_advancePrompt");
	DOM::append($advanceHd, $text);
DOM::append($advanceOptions, $advanceHd);

// Set selected branch
$title = moduleLiteral::get($moduleID, "lbl_branchName");
$input = $form->getInput("text", "branch", "master", "", TRUE, TRUE); // TODO temporary we set it statically
$row = $form->buildRow($title, $input);
DOM::append($advanceOptions, $row);

// Set Release Version
// TODO
// Automatically Calculate the 'next' release version
$website = new website($websiteID);
$releases = $website->getReleases();
$version = $releases[0]['version'] ;
//$version = "0.1.20";
$title = moduleLiteral::get($moduleID, "lbl_releaseVersion");
$input = $form->getInput("text", "releaseVer", $version, "", TRUE, TRUE);
$row = $form->buildRow($title, $input);
DOM::append($advanceOptions, $row);


// Commit Items Selector
DOM::append($advanceOptions, buildCommitSection($websiteID));




// Append content
$frame->append($pageContent->get());
// Return frame output
return $frame->getFrame();


/**
 * Builds the commit section.
 * 
 * @return	void
 */
function buildCommitSection($vcsID)
{
	$vcs = new vcs($vcsID);

	$commitSection = DOM::create("div", "", "", "commitSection");
	
	// Get working items
	$workingItems = $vcs->getWorkingItems();
	$authors = $vcs->getAuthors();
	
	// Commit Header
	$attr = array();
	$attr['count'] = "".count($workingItems);
	$commitTitle = literal::get("sdk.DEV.Version", "lbl_commitManager_commitItems", $attr);
	$commitHeader = DOM::create("h3", $commitTitle, "", "vcsHeader");
	DOM::append($commitSection, $commitHeader);
	
	if (count($workingItems) == 0)
	{
		$desc = literal::get("sdk.DEV.Version", "lbl_commitManager_noItems");
		$noItemsDesc = DOM::create("p", $desc);
		DOM::append($commitSection, $noItemsDesc);
		//return;
	}
	
	// Create commit list container
	$commitListContainer = DOM::create("div", "", "", "commitListContainer");
	DOM::append($commitSection, $commitListContainer);

	// Force commit items
	$forceItems = array();
	foreach ($workingItems as $id => $item)
		if ($item['force_commit'])
			$forceItems[$id] = $workingItems[$id];

	$ratios = array();
	$ratios[] = 0.6;
	$ratios[] = 0.2;
	$ratios[] = 0.2;
	
	$headers = array();
	$headers[] = literal::get("sdk.DEV.Version", "lbl_commitManager_itemPath", array(), FALSE);
	$headers[] = literal::get("sdk.DEV.Version", "lbl_commitManager_lastAuthor", array(), FALSE);
	$headers[] = literal::get("sdk.DEV.Version", "lbl_commitManager_lastUpdate", array(), FALSE);

	if (count($forceItems) > 0)
	{
		// Header
		$title = literal::get("sdk.DEV.Version", "lbl_commitManager_forceCommitItems");
		$header = DOM::create("h4", $title);
		DOM::append($commitListContainer, $header);
		
		// Force Commit Item List
		$dataList = new dataGridList();
		$fCommitList = $dataList->build($id = "fCommitItems", $checkable = FALSE)->get();
		DOM::append($commitListContainer, $fCommitList);
		
		$dataList->setColumnRatios($ratios);
		$dataList->setHeaders($headers);
		
		foreach ($forceItems as $id => $item)
		{
			$rowContents = array();
			$rowContents[] = $item['path'];
			$rowContents[] = $item['last-edit-author'];
			$rowContents[] = datetimer::live($item['last-edit-time'], $format = 'd F, Y \a\t H:i');
			$dataList->insertRow($rowContents);
		}
	}

	// Header
	$title = literal::get("sdk.DEV.Version", "lbl_commitManager_selectItems");
	$header = DOM::create("h4", $title);
	DOM::append($commitListContainer, $header);
	
	// Commit Item List
	$dataList = new dataGridList();
	$commitList = $dataList->build($id = "commitItems", $checkable = TRUE)->get();
	
	$dataList->setColumnRatios($ratios);
	$dataList->setHeaders($headers);

	foreach ($workingItems as $id => $item)
	{
		// Skip force commit items
		if (isset($forceItems[$id]))
			continue;
			
		$rowContents = array();
		$rowContents[] = $item['path'];
		$rowContents[] = $item['last-edit-author'];
		$rowContents[] = datetimer::live($item['last-edit-time'], $format = 'd F, Y \a\t H:i');
		$dataList->insertRow($rowContents, $checkName = "citem[".$id."]", $checked = TRUE); // All items are cheched to be commmited by default
	}
	
	DOM::append($commitListContainer, $commitList);	
	
	return $commitSection;
}
//#section_end#
?>