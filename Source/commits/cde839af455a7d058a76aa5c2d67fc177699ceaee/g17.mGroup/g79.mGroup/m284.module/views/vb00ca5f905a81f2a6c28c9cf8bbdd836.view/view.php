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
importer::import("API", "Comm");
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Resources");
importer::import("DEV", "Projects");
importer::import("DEV", "WebEngine");
importer::import("DEV", "Websites");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Comm\ftp; 
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Model\core\resource;
use \API\Profile\account;
use \API\Resources\archive\zipManager;
use \API\Resources\filesystem\directory;
use \API\Resources\filesystem\fileManager;
use \DEV\Projects\projectLibrary;
use \DEV\WebEngine\webCoreProject;
use \DEV\Websites\wsServer;
use \DEV\Websites\settings\wsSettings;
use \UI\Forms\templates\simpleForm;
use \UI\Modules\MContent;

// Get website id
$websiteID = engine::getVar('id');
$serverID = engine::getVar('srvid');
$releaseVersion = engine::getVar('version');
if (engine::isPost())
{
	// Set step number
	$step = 4;
	
	// Create Module Content
	$pageContent = new MContent($moduleID);
	
	// Build the module content
	$pageContent->build("", "websiteUploader");
	
	// Set step count
	$pageContent->addReportAction('website.setStep', $step);
	
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
	
	// TODO
	// Before continuing check the status of the server 
	// and decide what to do
	// - Clean Setup
	// - Core and website update
	// - Website update
	
	
	// Get server info
	$server = new wsServer($websiteID, $serverID);
	$serverInfo = $server->info();
	
	// TODO
	// Switch between different connection types
	// To load the suitable connector / client
	$ftp = new ftp();
	

	// Establish Connection with the remote server
	$status = $ftp->connect($serverInfo['address'], $serverInfo['username'], $serverInfo['password']); 
	logger::log("Connecting to server : ".($status ? "SUCCESS" : "FAILURE"), logger::DEBUG);
	
	// Navigate to httpdocs folder
	$status = $ftp->changeDir($serverInfo['httpdocs'], $createDir = TRUE);
	logger::log("Change to httpdocs dir : ".($status ? "SUCCESS" : "FAILURE"), logger::DEBUG);
	
	// Get the installation script folder
	// TODO
	// - Temporary installation scripts are located to Developer's Dynamic Resource Path
	
	// Get website production url
	$wsSettings = new wsSettings($websiteID);
	$siteUrl = $wsSettings->get('site_url');
	$webRoot = $wsSettings->get('web_root');
	if (!empty($webRoot) || $webRoot != "/")
		$ftp->changeDir($serverInfo['httpdocs']."/".$webRoot, $createDir = TRUE);
	
	// Set site production url
	$productionUrl = $siteUrl.$webRoot;
	
	// Upload the prepare.php file
	$filePath = resource::getPath("/resources/DEV/websites/scripts/prepare.inc", $rootRelative = FALSE);
	$status = $ftp->put($filePath, "prepare.php", "");
	logger::log("Put prepare file : ".($status ? "SUCCESS" : "FAILURE"), logger::DEBUG);
	
	// Initiate extraction process
	$response = doRequest($productionUrl."/prepare.php", $error);
	$responseArr = json_decode($response, TRUE);
	
	// If response is null, meaning the the remote server is
	// unaccessible exit the process and return
	if (empty($response) || !$responseArr['status'])
	{
		// Add error action
		$pageContent->addReportAction("website.error", $step);
		
		// Add error content
		$errorContent = moduleLiteral::get($moduleID, "lbl_ftpNoResponse");
		$pageContent->append($errorContent);
		
		// Return output
		return $pageContent->getReport(".wsPublisher .errorHolder", "replace");
	}
	
	// Upload setup file
	$filePath = resource::getPath("/resources/DEV/websites/scripts/setup.inc", $rootRelative = FALSE);
	$status = $ftp->put($filePath, "setup.php", "");
	logger::log("Put setup file : ".($status ? "SUCCESS" : "FAIL"), logger::DEBUG);
	
	// Upload web core to .temp folder
	$webCorePath = webCoreProject::getDistroPath($version = "", $rootRelative = FALSE);
	$status = $ftp->put($webCorePath, ".temp/wsdk.zip", "");
	logger::log("Uploading core zip file : ".($status ? "SUCCESS" : "FAIL"), logger::DEBUG);
	
	// Initiate setup process
	$response = doRequest($productionUrl."/setup.php", $error);
	$responseArr = json_decode($response, TRUE);
	logger::log("Initiate setup process : ".($responseArr['status'] ? "SUCCESS" : "FAILURE"), logger::DEBUG, print_r($responseArr['log'], TRUE));
	
	// Pack the latest website release	
	$publishFolder = systemRoot.projectLibrary::getPublishedPath($websiteID, $releaseVersion);
	$contents = directory::getContentList($publishFolder, TRUE, FALSE, FALSE);
	$archive = systemRoot.account::getServicesFolder('Web')."/website_".$releaseVersion.".zip";
	$s = zipManager::create($archive, $contents , TRUE, TRUE);
	
	// Upload website
	$status = $ftp->put($archive, ".temp/website.zip", '');
	logger::log("Uploading the Website Zip file : ".($status ? "SUCCESS" : "FAIL"), logger::DEBUG);
	
	// Upload update file
	$filePath = resource::getPath("/resources/DEV/websites/scripts/update.inc", $rootRelative = FALSE);
	$status = $ftp->put($filePath, ".wec/scripts/update.php", '');
	logger::log("Uploading Update.php script file : ".($status ? "SUCCESS" : "FAIL"), logger::DEBUG);
	
	
	// TODO
	// If this is a clean setup
	// Upload the root files
	if (TRUE)
	{
		$websiteConfig = resource::get("/resources/DEV/websites/__websiteConfig.inc");
		$websiteConfig  = str_replace('{siteInnerPath}', $webRoot, $websiteConfig);
		$status = $ftp->write("__websiteConfig.inc", $websiteConfig, '');
	}
	
	// TODO
	// Upload other page files like robots.txt, ...
	/*
	$oFile = {PATH_TO_ROBOTS_TXT};
	$dPath  = "robots.txt";
	$status = $ftp->put($oFile, $dPath, '');
	$cnt = "Uploading robots.txt file : - ";
	if($status)
		$cnt .= "SUCCESS";
	else
		$cnt .= "FAIL";
	$msg = DOM::create("p", $cnt);
	$pageContent->append($msg);
	*/
	
	// Close ftp connection
	$ftp->close();
	
	// Issue update.php to install the website
	$response = doRequest($productionUrl."/.wec/scripts/update.php", $error);
	$responseArr = json_decode($response, TRUE);
	logger::log("Updating / Installing Website : ".($responseArr['status'] ? "SUCCESS" : "FAILURE"), logger::DEBUG, print_r($responseArr['log'], TRUE));
	
	
	// Website publish is completed
	
	// Add action to add title
	$title = moduleLiteral::get($moduleID, "lbl_status_websitePublished", array(), FALSE);
	$pageContent->addReportAction('website.addStatusTitle', $title);
	
	// Set step ok and proceed to next form
	$pageContent->addReportAction("website.stepOK", $step);
	
	// Set last step as ok
	$pageContent->addReportAction("website.stepOK", $step+1);
	
	// Return output
	return $pageContent->getReport(".wsPublisher .formsHolder", "replace");
}


function doRequest($url, &$error)
{
	// Get current user agent to set for the request
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
	
	// Initialize url
	$curl = curl_init($url);
        if (is_bool($curl) && !$curl)
        {
            $error = 'Error Initializing cURL';
            return NULL;
        }
	
	// On curl_exec success return the fetch page (string)
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1 );
	// to use curl_getinfo() with option CURLINFO_HEADER_OUT in order to debug your cURL request
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);

	// Set request extra options
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 16 );
	
	// Create request and get extra information
        $response = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlInfo = curl_getinfo($curl, CURLINFO_HEADER_OUT);

	// Check for error
	$error = '';
        if (is_bool($response) && !$response)
        {
            $error .= "curl_exec error\n" ;
            $response = NULL;
            // Check for errors and display the error message
            if($errno = curl_errno($curl))
            {
                $error_message = curl_strerror($errno);
                $error .= "cURL error ({$errno}): {$error_message}\n" ;
                $error .= "Curl error: ".curl_error($curl)."\n" ;
            }
            $error .= "Curl Info\n" ;
            $error .= print_r($curlInfo, true)."\n" ;
        }

	// If response is not 200
        if ($http_status != 200)
        {
            $error .= 'The server responded: ' . $http_status."\n" ;
            $response = NULL;
        }

	// Close connection and return response
        curl_close($curl);
	return $response;
}
//#section_end#
?>