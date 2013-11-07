<?php
/**
 * Index file
 *
 * This file includes all 'include files', loads modules
 * and gets output of default module.
 * @author Paul Bukowski <pbukowski@telaxus.com>
 * @copyright Copyright &copy; 2006, Telaxus LLC
 * @license MIT
 * @version 1.0 
 * @package dokec-base
 */
if(version_compare(phpversion(), '5.0.0')==-1)
	die("You are running an old version of PHP, php5 required.");

if(trim(ini_get("safe_mode")))
	die('You cannot use DOKEC with PHP safe mode turned on - please disable it. Please notice this feature is deprecated since PHP 5.3 and will be removed in PHP 6.0.');

define('_VALID_ACCESS',1);
require_once('include/data_dir.php');
if(!file_exists(DATA_DIR.'/config.php')) {
	die('Invalid address');
	exit();
}

if(!is_writable(DATA_DIR))
	die('Cannot write into "'.DATA_DIR.'" directory. Please fix privileges.');

// require_once('include/include_path.php');
require_once('include/config.php');
require_once('include/error.php');
ob_start(array('ErrorHandler','handle_fatal'));
require_once('include/database.php');
require_once('include/variables.php');

$tables = DB::MetaTables();
if(!in_array('modules',$tables) || !in_array('variables',$tables) || !in_array('session',$tables))
	die('Database structure you are using is apparently out of date or damaged. If you didn\'t perform application update recently you should try to restore the database. Otherwise, please refer to DOKEC documentation in order to perform database update.');

require_once('include/misc.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

		<head profile="http://www.w3.org/2005/11/profile">
		<link rel="icon" type="image/png" href="images/favicon.png" />
		<link rel="apple-touch-icon" href="images/apple-favicon.png" />
		<title>71srm</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=8" />
		<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
		<meta content="71SRM,企业供应商关系管理,帮助企业与供应商更好的实现协作共赢." name="description"> 
<?php
	ini_set('include_path','libs/minify'.PATH_SEPARATOR.'.'.PATH_SEPARATOR.'libs'.PATH_SEPARATOR.ini_get('include_path'));
	require_once('Minify/Build.php');
	$jses = array('libs/prototype.js','libs/jquery-1.7.2.min.js','libs/jquery-ui-1.8.21.custom.min.js','libs/jquery.misc.js','libs/HistoryKeeper.js','include/dokec.js','include/interval.js','libs/notification/alertify.min.js');
	$jsses_build = new Minify_Build($jses);
	$jsses_src = $jsses_build->uri('serve.php?'.http_build_query(array('f'=>array_values($jses))));
?>
		<script type="text/javascript" src="<?php print($jsses_src)?>"></script>
<?php
	$csses = array('libs/jquery-ui-1.8.21.custom.css','libs/notification/themes/alertify.css','libs/notification/themes/alertify.default.css','libs/font-awesome.min.css');
	$csses_build = new Minify_Build($csses);
	$csses_src = $csses_build->uri('serve.php?'.http_build_query(array('f'=>array_values($csses))));
?>
		<link type="text/css" href="<?php print($csses_src)?>" rel="stylesheet"></link>

		<style type="text/css">
			<?php if (DIRECTION_RTL) print('body { direction: rtl; }'); ?>
			#dokecStatus {
  				/* Netscape 4, IE 4.x-5.0/Win and other lesser browsers will use this */
  				position: absolute;
  				left: 50%; top: 30%;
                margin-left: -280px;
  				/* all */
  				/*background-color: #e6ecf2;*/
  				background-color: #f5f5f5;
				border: 1px solid #e5e5e5;
				visibility: hidden;
				width: 560px;
				text-align: center;
				vertical-align: middle;
				z-index: 2002;
                color: #336699;
			}
			#dokecStatus table {
				color: #336699;
				font-weight: bold;
				font-family: Tahoma, Verdana, Vera-Sans, DejaVu-Sans;
				font-size: 11px;
				border: 5px solid #FFFFFF;
            }

		</style>
		<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-35907223-3']);
		_gaq.push(['_setSiteSpeedSampleRate', 100]);
		_gaq.push(['_setDomainName', '71srm.com']);
		_gaq.push(['_trackPageview']);
		_gaq.push(['_trackPageLoadTime']);
		</script>
		<script type="text/javascript" src="//assets.zendesk.com/external/zenbox/v2.6/zenbox.js"></script>
		<style type="text/css" media="screen, projection">
  @import url(//assets.zendesk.com/external/zenbox/v2.6/zenbox.css);
</style>
		<?php if( extension_loaded('newrelic') ) { echo newrelic_get_browser_timing_header(); } ?>
	</head>
	<body id="71srm_main_body" <?php if (DIRECTION_RTL) print('class="dokec_rtl"'); ?> >

		<div id="body_content">
			<div id="main_content" style="display:none;"></div>
			<div id="debug_content" style="padding-top:97px;display:none;">
				<div class="button" onclick="$('error_box').innerHTML='';$('debug_content').style.display='none';">Hide</div>
				<div id="debug"></div>
				<div id="error_box"></div>
			</div>
			
			<div id="dokecStatus">
				<table cellspacing="0" cellpadding="0" border="0" style="width: 100%;">
					<tr>
						<td><img src="images/logo.png" alt="logo" border="0"></td>
					</tr>
					<tr>
						<td style="text-align: center; vertical-align: middle; height: 30px;"><span id="dokecStatusText">Starting 71SRM ...</span></td>
					</tr>
					<tr>
						<td style="text-align: center; vertical-align: middle; height: 30px;"><img src="images/loader.gif" alt="loader" width="256" height="10" border="0"></td>
					</tr>
				</table>
			</div>	
		</div>
		<script type="text/javascript" src="init_js.php?<?php print(http_build_query($_GET));?>"></script>
		<script type="text/javascript">
		(function() {
			  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">google.load('visualization', '1.0', {'packages':['corechart']});</script>
        <noscript>Please enable JavaScript in your browser and let 71SRM work!</noscript>
		<?php if(IPHONE) { ?>
		<script type="text/javascript">var iphone=true;</script>
		<?php } ?>
		<?php if( extension_loaded('newrelic') ) { echo newrelic_get_browser_timing_footer(); } ?>
	</body>
</html>
<?php
$content = ob_get_contents();
ob_end_clean();

require_once('libs/minify/HTTP/Encoder.php');
$he = new HTTP_Encoder(array('content' => $content));
if (MINIFY_ENCODE)
	$he->encode();
$he->sendAll();
?>
