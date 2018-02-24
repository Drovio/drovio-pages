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
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("DEV", "WebEngine");
importer::import("DEV", "Websites");
importer::import("DEV", "Resources");
//#section_end#
//#section#[code]
use \API\Comm\ftp; 
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
$websiteID = $_REQUEST['id'];
$srvID = $_REQUEST['srvID'];
$releaseVer = $_REQUEST['releaseVer'];
if (engine::isPost())
{	
	// Packs / Uploads / and Installs the website
	// Returns a summary indicating the success or failure of the Install Proccess
	
	// Create Module Page
	$pageContent = new MContent($moduleID);
	$actionFactory = $pageContent->getActionFactory();
	
	// Build the module content
	$pageContent->build("", "uc");
	
	
	// Get the server settings
	$server = new wsServer($websiteID, $srvID);
	$info = $server->info();
	
	// TODO
	// Before continuing check the status of the server 
	// and decide what to do
	// - Clean Setup
	// - Core and website update
	// - Website update
	
	
	
	// TODO
	// Switch between different connection types

	// To load the suitable connector / client
	$ftp = new ftp();
	

	// Establish Connection with the remote server
	//$status = $ftp->connect('ftp.skyworks.gr', 'n37408naps', 'yH*29@dW');
	$status = $ftp->connect($info['address'], $info['username'], $info['password']); 
	$cnt = "Conection to ".$info['address']; 
	if($status)
		$cnt .= " succeded";
	else
		$cnt .= " failed";
	$msg = DOM::create("p", $cnt);
	$pageContent->append($msg);
	
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
	$oFile = $coreDir.'/prepare.php';
	$dPath = 'prepare.php';
	$status = $ftp->put($oFile, $dPath, '');	
	$cnt = "Uploading prepare.php file : - ";
	if($status)
		$cnt .= "SUCCESS";
	else
		$cnt .= "FAIL";
	$msg = DOM::create("p", $cnt);
	$pageContent->append($msg);	
	
	// if no error
	// Get website Url
	$wsSettings = new wsSettings($websiteID); 
	$url = $wsSettings->get('url')."/".$info['webroot'];
	// Initiate extraction proccess
	$response = doRequest($url."/prepare.php", $error); //"www.skyworks.gr/test/prepare.php"
	
	if(is_null($response))
		$msg = DOM::create("p", "FCK ".$error);
	else
		$msg = DOM::create("p", $response);
	$pageContent->append($msg);
	
	if(is_null($response))
		$pageContent->getReport();
	
	// If everything ok -> continue
	
	// Upload setup file
	$oFile = $coreDir.'/setup.php';
	$dPath = 'setup.php';
	$status = $ftp->put($oFile, $dPath, '');
	if($status)
		$msg = DOM::create("p", "OK");
	else
		$msg = DOM::create("p", "FCK");
	$pageContent->append($msg);
	
	
	// Upload core zip file to .temp folder	
	// Get the forlder that Core is placed
	// TODO
	// - wsdk.zip file version needs to be defined and attached to the name
	// - The path '/System/Library/WebDistros/' is adhoc need to be taken from somewhere
	// Latest version 1/12 -> wsdk_0.1.14.zip
	$webCoreProject= new webCoreProject(); 
	$releases = $webCoreProject->getReleases();
	$version = $releases[0]['version'];	
	$oFile = systemRoot."/System/Library/WebDistros/"."wsdk_".$version.".zip";
	$dPath = '.temp/wsdk.zip';
	
	// TODO
	// - Check whenever we need to change the current ftp dir, in most cases of upload file
	// it does not makes a difference (ftp_chdir($conn, 'httpdocs/test/.temp');)
	$status = $ftp->put($oFile, $dPath, '');
	if($status)
		$msg = DOM::create("p", "OK");
	else
		$msg = DOM::create("p", "FCK");
	$pageContent->append($msg);
	
	// if no error
	// Initiate setup proccess
	$response = doRequest($url."/setup.php", $error); //"www.skyworks.gr/test/setup.php"	
	if(is_null($response))
		$msg = DOM::create("p", "FCK ".$error);
	else
		$msg = DOM::create("p", $response);
	$pageContent->append($msg);
	
	// Pack the latest website release
	
	$publishFolder = systemRoot.projectLibrary::getPublishedPath($websiteID, $releaseVer);	
	$contents = directory::getContentList($publishFolder, TRUE, TRUE, FALSE); // TODO somewhere here there is an error
	$serviceName = 'Web';
	$archive = systemRoot.account::getServicesFolder($serviceName)."/website_".$releaseVer.".zip";
	$s = zipManager::create($archive, $contents , TRUE, TRUE);
	
	
	
	// Upload the zip file
	$oFile = $archive;
	$dPath = '.temp/website.zip';
	
	// TODO
	// - Check whenever we need to change the current ftp dir, in most cases of upload file
	// it does not makes a difference (ftp_chdir($conn, 'httpdocs/test/.temp');)
	$status = $ftp->put($oFile, $dPath, '');
	if($status)
		$msg = DOM::create("p", "OK ".$publishFolder);
	else
		$msg = DOM::create("p", "FCK");
	$pageContent->append($msg);
	
	// Upload update file
	$oFile = $coreDir.'/update.php';
	$dPath = '.wec/scripts/update.php';
	$status = $ftp->put($oFile, $dPath, '');
	$cnt = "Uploading Update.php script file : - ";  
	if($status)
		$cnt .= "SUCCESS";
	else
		$cnt .= "FAIL";
	$msg = DOM::create("p", $cnt);
	$pageContent->append($msg);
	
	
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
		$pageContent->append($msg);
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
	$cnt = "Updating / Installing Website : - "; 
	$response = doRequest($url."/.wec/scripts/update.php", $error);	
	if(is_null($response))		
		$cnt .= "FAIL ".$error;
	else
		$cnt .= "SUCCESS ".$response;
	$msg = DOM::create("p", $cnt);
	$pageContent->append($msg);
	
	
	// Add Action	
	$pageContent->addReportAction('website.publish.finish');
	
	// Return output
	return $pageContent->getReport('.summaryLog');
}

// Assuming Module will be loaded using load:view


// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "websiteUploaderContent", TRUE);

// Build Form
$form = new simpleForm();
$form->build('', FALSE)->engageModule($moduleID, "websiteUploader");
$formHolder = HTML::select('.section.formHolder')->item(0);
DOM::append($formHolder, $form->get());
//$pageContent->append($form->get());

// Set website id
$input = $form->getInput("hidden", "id", $websiteID, "", TRUE, TRUE);
$form->append($input);

// Set selected server id
$input = $form->getInput("hidden", "srvID", $srvID, "", TRUE, TRUE);
$form->append($input);

// Set selected release version
$input = $form->getInput("hidden", "releaseVer", $releaseVer, "", TRUE, TRUE);
$form->append($input);

// Return output
return $pageContent->getReport();


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