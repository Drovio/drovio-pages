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
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("DEV", "Version");
importer::import("DEV", "Websites");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Geoloc\datetimer;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Profile\account;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Modules\MContent;
use \UI\Presentation\frames\windowFrame;
use \UI\Presentation\dataGridList;
use \DEV\Version\vcs;
use \DEV\Websites\website;
use \DEV\Websites\wsServer;
use \DEV\Websites\settings\wsSettings;

// Get website ID
$websiteID = engine::getVar('id');
$website = new website($websiteID);
if (engine::isPost())
{
	// Set step number
	$step = 1;
	
	// Create Module Content
	$pageContent = new MContent($moduleID);
	
	// Build the module content
	$pageContent->build("", "repositoryPublisher");
	
	// Set step count
	$pageContent->addReportAction('website.setStep', $step);
	
	// Add action to add status title
	$title = moduleLiteral::get($moduleID, "lbl_status_preparingPublish", array(), FALSE);
	$pageContent->addReportAction('website.addStatusTitle', $title);
	
	// Validate form post
	if (!simpleForm::validate())
	{
		// Add error action
		$pageContent->addReportAction("website.error", $step);
		
		// Add error content
		$errorContent = moduleLiteral::get($moduleID, "hd_formValidateError");
		$pageContent->append($errorContent);
		
		// Return output
		return $pageContent->getReport(".wsPublisher .errorHolder", "replace");
	}
	
	// Authenticate account
	$username = account::getUsername(TRUE);
	$password = $_POST['password'];
	if (!account::authenticate($username, $password))
	{
		// Add error action
		$pageContent->addReportAction("website.error", $step);
		
		// Add error content
		$errorContent = moduleLiteral::get($moduleID, "authentication_error_header");
		$pageContent->append($errorContent);
		
		// Return output
		return $pageContent->getReport(".wsPublisher .errorHolder", "replace");
	}
	
	// Get website online
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "get_website_online");
	$attr = array();
	$attr['pid'] = $websiteID;
	$attr['status'] = 1;
	$dbc->execute($q, $attr);
	
	// Get the user input that configures the publish process
	// Create form to start the source release process
	
	// Build Form
	$form = new simpleForm();
	$releaseSourceForm = $form->build("", FALSE)->engageModule($moduleID, "releaseSource")->get();
	$pageContent->append($releaseSourceForm);
	
	// Set website id
	$input = $form->getInput("hidden", "id", $websiteID, "", FALSE, FALSE);
	$form->append($input);
	
	// Set selected server id
	$input = $form->getInput("hidden", "srvid", engine::getVar("srvid"), "", FALSE, FALSE);
	$form->append($input);
	
	// Set selected branch
	$input = $form->getInput("hidden", "branch", engine::getVar("branch"), "", FALSE, FALSE); 
	$form->append($input);
	
	// Set selected release title
	$input = $form->getInput("hidden", "title", engine::getVar("title"), "", FALSE, FALSE);
	$form->append($input);
	
	// Set selected release title
	$input = $form->getInput("hidden", "changelog", engine::getVar("changelog"), "", FALSE, FALSE); 
	$form->append($input);
	
	// Set Release Version
	$input = $form->getInput("hidden", "version", engine::getVar("version"), "", FALSE, FALSE);
	$form->append($input);
	
	// The Items selected for commit
	$commitItems = array();
	$postItems = $_POST['citem'];
	foreach ($postItems as $id => $content)
		$commitItems[] = $id;
	$serializable = json_encode($commitItems);
	$input = $form->getInput("hidden", "citem_ser", $serializable, "", TRUE, TRUE);
	$form->append($input);
	
	// Add action to add status title
	$title = moduleLiteral::get($moduleID, "lbl_status_releaseSource", array(), FALSE);
	$pageContent->addReportAction('website.addStatusTitle', $title);
	
	// Set step ok and proceed to next form
	$pageContent->addReportAction("website.stepOK", $step);
	
	// Return output
	return $pageContent->getReport(".wsPublisher .formsHolder", "replace");
}




// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "wsPublisherDialog", TRUE);

// Build Form
$form = new simpleForm();
$form->build()->engageModule($moduleID, "websitePublisher");


// Get publisher into the form
$publisher = HTML::select(".wsPublisher .publisher")->item(0);
$form->append($publisher);

// Prepend form to wsPublisher
$wsPublisher = HTML::select(".wsPublisher")->item(0);
DOM::append($wsPublisher, $form->get());
$mainForm = HTML::select(".wsPublisher .mainForm")->item(0);

// Set website id
$input = $form->getInput("hidden", "id", $websiteID, "", TRUE, TRUE);
$form->append($input);

// Relase Title
$websiteInfo = $website->info();
$title = moduleLiteral::get($moduleID, "lbl_releaseTitle");
$input = $form->getInput($type = "text", $name = "title", $value = $websiteInfo['title'], $class = "", $autofocus = TRUE, $required = TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($mainForm, $inputRow);

// Release Changelog 
$title = moduleLiteral::get($moduleID, "lbl_releaseChangelog");
$notes = moduleLiteral::get($moduleID, "lbl_releaseChangelog_notes");
$input = $form->getTextArea("changelog", "", "", FALSE);
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes);
DOM::append($mainForm, $inputRow);

// Select Server
$wsServer = new wsServer($websiteID);
$servers = $wsServer->getServerList();
$resource = array();
foreach ($servers as $id => $srv)
	$resource[$id] = $srv['name'];
	
$title = moduleLiteral::get($moduleID, 'lbl_selectServer');
$input = $form->getResourceSelect("srvid", FALSE, "", $resource, "");
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($mainForm, $inputRow);

// Account authentication
$title = moduleLiteral::get($moduleID, "lbl_accountPassword");
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($mainForm, $inputRow);

// Advanced Options
$advancedForm = HTML::select(".wsPublisher .advancedSettingsContainer .advancedSettings")->item(0);

// Source branch to release
$vcs = new vcs($websiteID);
$branches = $vcs->getBranches();
$branchResource = array();
foreach ($branches as $branchName => $branchInfo)
	$branchResource[$branchName] = $branchName;
$workingBranch = $vcs->getWorkingBranch();
$title = moduleLiteral::get($moduleID, "lbl_branchName");
$input = $form->getResourceSelect($name = "branch", $multiple = FALSE, $class = "", $branchResource, $selectedValue = $workingBranch);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
DOM::append($advancedForm, $inputRow);

// Release version
// Get current version
$releases = $website->getReleases();
$currentVersion = $releases[0]['version'];
// Calculate next version
$versionParts = explode(".", $currentVersion);
$versionParts[count($versionParts)-1] = $versionParts[count($versionParts)-1]+1;
$nextVersion = implode(".", $versionParts);
$title = moduleLiteral::get($moduleID, "lbl_releaseVersion");
$attr = array();
$attr['version'] = $currentVersion;
$notes = moduleLiteral::get($moduleID, "lbl_releaseVersion_notes", $attr);
$input = $form->getInput("text", "version", $nextVersion, "", TRUE, TRUE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes);
DOM::append($advancedForm, $inputRow);

// Site url
$wsSettings = new wsSettings($websiteID);
$title = moduleLiteral::get($moduleID, "lbl_settings_siteurl");
$label = DOM::create("span", $wsSettings->get("site_url"), "", "lspan");
$inputRow = $form->buildRow($title, $label, $required = FALSE, $notes = "");
DOM::append($advancedForm, $inputRow);

// Website root folder
$webRoot = $wsSettings->get("web_root");
$webRoot = (empty($webRoot) ? "/" : $webRoot);
$title = moduleLiteral::get($moduleID, "lbl_settings_webroot");
$label = DOM::create("span", $webRoot, "", "lspan");
$inputRow = $form->buildRow($title, $label, $required = FALSE, $notes = "");
DOM::append($advancedForm, $inputRow);

// Commit Items Selector
$commitSection = buildCommitSection($websiteID);
DOM::append($advancedForm, $commitSection);

// Build frame
$frame = new windowFrame();
$title = moduleLiteral::get($moduleID, "title");
$frame->build($title, "wsPublisherFrame");
$frame->append($pageContent->get());
return $frame->getFrame();



function buildCommitSection($vcsID)
{
	$vcs = new vcs($vcsID);

	$commitSection = DOM::create("div", "", "", "commitSection");
	
	// Get working items
	$authors = $vcs->getAuthors();
	$workingItems = $vcs->getWorkingItems();
	if (count($workingItems) == 0)
	{
		$desc = literal::get("sdk.DEV.Version", "lbl_commitManager_noItems");
		$noItemsDesc = DOM::create("p", $desc);
		DOM::append($commitSection, $noItemsDesc);
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
	
	// Check if there are normal commit items
	if (count($workingItems) - count($forceItems) <= 0)
		return $commitSection;

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