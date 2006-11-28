<?php
$errorMsg = sprintf( 'The script createT3X needs 2 parameters.%sPATH_SVN_EXPORT = The path to the export folder.%sEXTENSION_KEY = The extension key.%s', chr( 10 ), chr( 10 ), chr( 10 ) );
if ( !isset( $_SERVER['argv'][1] ) && !isset( $_SERVER['argv'][2] ) ) {
	die( $errorMsg );
}

	// Define PATH_thisScript
define('PATH_thisScript',$_SERVER['argv'][0]);

	// Change working directory to the directory of the script.
chdir( dirname( PATH_thisScript ) );

$BASE_DIR = dirname( PATH_thisScript );
$SOURCES_DIR = $_SERVER['argv'][1];
$EXTENSION_KEY = $_SERVER['argv'][2];

define('PATH_site', $SOURCES_DIR);

require_once($BASE_DIR.'/class.t3lib_div.php');
require_once($BASE_DIR.'/extensionManager.php');

$GLOBALS['TYPO3_CONF_VARS']['BE']['fileCreateMask'] = 770;

$em = t3lib_div::makeInstance('tx_t3xmaker');
$em->typePaths = array('L' => '/');
$em->kbMax = 8192;

$list = array();
$cat = array();
// $em->getInstExtList( PATH_site.'/', $list, $cat, 'L' );
$em->getExtDetails( PATH_site.'/'.$EXTENSION_KEY, $EXTENSION_KEY, $list, $cat, 'L' );

$date = date( 'Y-m-d' );

ksort( $list );

foreach ($list as $extKey => $extInfo)	{
	$uArr = $em->makeUploadArray($extKey,$extInfo);
	if (is_array($uArr)) {
		$backUpData = $em->makeUploadDataFromArray($uArr,1);
		$version = preg_replace('/[^0-9]+/', '_', $extInfo['EM_CONF']['version']);
		t3lib_div::writeFile($SOURCES_DIR.'/T3X/T3X_'.$extKey.'-'.$version.'-'.$date.'.t3x', $backUpData);
	} else {
		printf( 'ERROR: Could not get dataArray for %s!%s', $extKey, chr( 10 ) );
	}
}

?>
