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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("DEV", "WebEngine");
importer::import("DEV", "Websites");
importer::import("DEV", "Resources");
//#section_end#
//#section#[code]
use \API\Comm\ftp; 
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Profile\account;
use \API\Resources\archive\zipManager;
use \API\Resources\filesystem\directory;
use \API\Resources\filesystem\fileManager;
use \DEV\Projects\projectLibrary;
use \DEV\Resources\paths;
use \DEV\WebEngine\webCoreProject;
use \DEV\Websites\wsServer;
use \DEV\Websites\wsSettings;
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
	$info = $server->info();
	
	// TODO
	// Switch between different connection types
	// To load the suitable connector / client
	$ftp = new ftp();
	

	// Establish Connection with the remote server
	$status = $ftp->connect($info['address'], $info['username'], $info['password']); 
	logger::log("Conection to ".$info['address']." ".($status ? "SUCCESS" : "FAIL"), logger::DEBUG);
	
	// Get connection handler / socket
	$conn = $ftp->getConnection();
	
	// Navigate to the correct http root folder
	// Usually : 'httpdocs'
	ftp_chdir($conn, $info['httpdocs']);
	
	// Get the installation script folder
	// TODO
	// - Temporary installation scripts are located to Developer's Dynamic Resource Path
	$coreDir = systemRoot.paths::getDevRsrcPath()."/websites/scripts";
	
	// Upload the prepare.php file
	$status = $ftp->put($coreDir."/prepare.php", "prepare.php", "");
	logger::log("Uploading prepare file : ".($status ? "SUCCESS" : "FAIL"), logger::DEBUG);
	
	// if no error
	// Get website Url
	$wsSettings = new wsSettings($websiteID); 
	$url = $wsSettings->get('url')."/".$info['webroot'];
	// Initiate extraction proccess
	$response = doRequest($url."/prepare.php", $error);
	logger::log("Executing prepare proccess : ".(is_null($response) ? "FAIL: ".$error : "SUCCESS: ".$response), logger::DEBUG);
	
	// If response is null, meaning the the remote server is
	// unaccessible exit the proccess and return
	if (is_null($response))
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
	$status = $ftp->put($coreDir."/setup.php", "setup.php", "");
	logger::log("Uploading setup file : ".($status ? "SUCCESS" : "FAIL"), logger::DEBUG);
	
	
	// Upload core zip file to .temp folder	
	// Get the forlder that Core is placed
	// TODO
	// - The path '/System/Library/WebDistros/' is adhoc need to be taken from somewhere
	$webCoreProject= new webCoreProject(); 
	$releases = $webCoreProject->getReleases();
	$version = $releases[0]['version'];
	
	// TODO
	// - Check whenever we need to change the current ftp dir, in most cases of upload file
	// it does not makes a difference (ftp_chdir($conn, 'httpdocs/test/.temp');)
	$status = $ftp->put(systemRoot."/System/Library/WebDistros/wsdk_".$version.".zip", ".temp/wsdk.zip", "");
	logger::log("Uploading core zip file : ".($status ? "SUCCESS" : "FAIL"), logger::DEBUG);
	
	// if no error
	// Initiate setup proccess
	$response = doRequest($url."/setup.php", $error);
	logger::log("Initiate setup proccess : ".(is_null($response) ? "FAIL: ".$error : "SUCCESS: ".$response), logger::DEBUG);
	
	
	// Pack the latest website release	
	$publishFolder = systemRoot.projectLibrary::getPublishedPath($websiteID, $releaseVersion);	
	$contents = directory::getContentList($publishFolder, TRUE, TRUE, FALSE);
	$archive = systemRoot.account::getServicesFolder('Web')."/website_".$releaseVersion.".zip";
	$s = zipManager::create($archive, $contents , TRUE, TRUE);
	
	// Upload the Website Zip file
	$oFile = $archive;
	$dPath = '.temp/website.zip';
	
	// TODO
	// - Check whenever we need to change the current ftp dir, in most cases of upload file
	// it does not makes a difference (ftp_chdir($conn, 'httpdocs/test/.temp');)
	$status = $ftp->put($oFile, $dPath, '');
	logger::log("Uploading the Website Zip file : ".($status ? "SUCCESS" : "FAIL"), logger::DEBUG);
	
	// Upload update file
	$oFile = $coreDir.'/update.php';
	$dPath = '.wec/scripts/update.php';
	$status = $ftp->put($oFile, $dPath, '');
	logger::log("Uploading Update.php script file : ".($status ? "SUCCESS" : "FAIL"), logger::DEBUG);
	
	
	// TODO
	// If this is a clean setup
	// Upload the root files
	if(TRUE)
	{
		//-		
		$contents = fileManager::get(systemRoot.paths::getDevRsrcPath()."/websites/__websiteConfig.inc");
		$contents  = str_replace('{siteInnerPath}', $info['webroot'], $contents);
		$dPath  = "__websiteConfig.inc";
		$status = $ftp->write($dPath, $contents, '');
		$cnt = "Creating websiteConfig file : - ";
		if($status)
			$cnt .= "SUCCESS";
		else
			$cnt .= "FAIL";
		$msg = DOM::create("p", $cnt);
		//$pageContent->append($msg);
	}
	
	// TODO
	// Upload robots.txt
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
	
	$ftp->close();
	
	// Delete temporary files
	//fileManager::remove($archive); // FAILS due to access control
	
	// Issue update.php to install the website
	$response = doRequest($url."/.wec/scripts/update.php", $error);	
	logger::log("Updating / Installing Website : ".(is_null($response) ? "FAIL: ".$error : "SUCCESS: ".$response), logger::DEBUG);
	
	
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
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

	$curl = curl_init($url);
        if(is_bool($curl) && !$curl)
        {
            $error = 'Error Initializing cURL';
            return NULL;
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1 ); // On curl_exec success return the fetch page (string)
        curl_setopt($curl, CURLINFO_HEADER_OUT, true); // to use curl_getinfo() with option CURLINFO_HEADER_OUT in order to debug your cURL request

        //curl_setopt($curl, CURLOPT_HEADER, true);    // we want headers
        //curl_setopt($curl, CURLOPT_NOBODY, true);    // we don't need body

        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 16 );

        $response = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlInfo = curl_getinfo($curl, CURLINFO_HEADER_OUT);

	$error = '';
        if(is_bool($response) && !$response)
        {
            //echo 'curl_exec error';
            $error .= 'curl_exec error'."\n \r" ;
            $response = NULL;
            // Check for errors and display the error message
            if($errno = curl_errno($curl))
            {
                $error_message = curl_strerror($errno);
                $error .= 'cURL error ({$errno}): {$error_message}'."\n \r" ;
                $error .= 'Curl error: ' . curl_error($curl)."\n \r" ;
            }
            $error .= 'Curl Info'."\n \r" ;
            $error .= print_r($curlInfo, true)."\n \r" ;
        }

        if( $http_status != 200)
        {
            $error .= 'The server responded: ' . $http_status."\n \r" ;
            $response = NULL;
        }

        curl_close($curl);

	return $response;
}
//#section_end#
?>