<?php
/*
  +----------------------------------------------------------------------+
  | APC                                                                  |
  +----------------------------------------------------------------------+
  | Copyright (c) 2006-2011 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Authors: Ralf Becker <beckerr@php.net>                               |
  |          Rasmus Lerdorf <rasmus@php.net>                             |
  |          Ilia Alshanetsky <ilia@prohost.org>                         |
  +----------------------------------------------------------------------+

   All other licensing and usage conditions are those of the PHP Group.
 */

$VERSION='$Id$';

$time = time();
$host = php_uname('n');

if(!function_exists('apcu_cache_info')) {
    echo "No cache info available.  APC does not appear to be running.";
    exit;
}

if($_SERVER['REMOTE_ADDR'] != "127.0.0.1") {
    echo "Access restricted to localhost";
    exit;
}

$cache = apcu_cache_info();

$mem = apcu_sma_info();

// don't cache this page
//
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");                                    // HTTP/1.0

// pretty printer for byte values
//
function bsize($s) {
  foreach (array('','K','M','G') as $i => $k) {
    if ($s < 1024) break;
    $s/=1024;
  }
  return sprintf("%5.1f %sBytes",$s,$k);
}

// Variables set and calculated by original apc.php
$mem_size = $mem['num_seg']*$mem['seg_size'];
$mem_avail= $mem['avail_mem'];
$mem_used = $mem_size-$mem_avail;
$seg_size = bsize($mem['seg_size']);
$req_rate_user = sprintf("%.2f", $cache['num_hits'] ? (($cache['num_hits']+$cache['num_misses'])/($time-$cache['start_time'])) : 0);
$hit_rate_user = sprintf("%.2f", $cache['num_hits'] ? (($cache['num_hits'])/($time-$cache['start_time'])) : 0);
$miss_rate_user = sprintf("%.2f", $cache['num_misses'] ? (($cache['num_misses'])/($time-$cache['start_time'])) : 0);
$insert_rate_user = sprintf("%.2f", $cache['num_inserts'] ? (($cache['num_inserts'])/($time-$cache['start_time'])) : 0);
$apcversion = phpversion('apcu');
$phpversion = phpversion();
$number_vars = $cache['num_entries'];
$size_vars = bsize($cache['mem_size']);

$result = array();

// General Cache Info
$general_cache_info = array('general_cache_info' => array('apcu_version' => $apcversion,
                                                          'php_version' => $phpversion));
$result = array_merge($result, $general_cache_info);

// ACPu Host
if(!empty($_SERVER['SERVER_NAME']))
    $apcu_host = array('apcu_host' => $_SERVER['SERVER_NAME']);
    $result = array_merge($result, $apcu_host);

// Server Software
if(!empty($_SERVER['SERVER_SOFTWARE']))
    $server_software = array('server_software' => $_SERVER['SERVER_SOFTWARE']);
    $result = array_merge($result, $server_software);


// Shared Memory Stats
$shared_memory = array('shared_memory_stats' => array('num_seg' => $mem['num_seg'],
                                                'set_size' => $seg_size,
                                                'memory_type' => $cache['memory_type']));
$result = array_merge($result, $shared_memory);


// Cache Stats
$cache_info = array('cache_stats' => array('number_vars' => $number_vars,
                                          'size_vars' => $size_vars,
                                          'hits' => $cache['num_hits'],
                                          'misses' => $cache['num_misses'],
                                          'request_rate' => $req_rate_user,
                                          'hit_rate' => $hit_rate_user,
                                          'miss_rate' => $miss_rate_user,
                                          'insert_rate' => $insert_rate_user,
                                          'num_expunges' => $cache['num_expunges']));
$result = array_merge($result, $cache_info);


// APCu Runtime Settings
$apcu_ini_local_values = array();
foreach (ini_get_all('apcu') as $k => $v) {
    $local_value = array($k => $v['local_value']);
    $apcu_ini_local_values = array_merge($apcu_ini_local_values, $local_value);
}
$apcu_runtime_settings = array('apcu_runtime_settings' => $apcu_ini_local_values);
$result = array_merge($result, $apcu_runtime_settings);


// Memory Usage Stats
$memory_usage = array('memory_usage_stats' => array('free' => bsize($mem_avail),
                                              'free_percent' => sprintf("%.1f%%",$mem_avail*100/$mem_size),
                                              'hits' => $cache['num_hits'],
                                              'used' => bsize($mem_used),
                                              'used_percent' => sprintf("%.1f%%",$mem_used*100/$mem_size),
                                              'misses' => $cache['num_misses']));
$result = array_merge($result, $memory_usage);


print_r(json_encode($result, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) . "\n");
?>
