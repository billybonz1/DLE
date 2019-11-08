<?php

if( ! defined( 'DATALIFEENGINE' ) ) {
    die( "Hacking attempt!" );
}

if ($config['allow_cache'] == "yes") {

$time = $time ? $time : 10;

$time_cache = filemtime ( ENGINE_DIR . '/cache/system/cron.php' );
$after_cache = (time() - $time_cache)/60;
$time_clean = $time - $after_cache;

if($after_cache >= $time) clear_cache();

echo "\n<!-- Кэш живет ".$after_cache." минуты -->\r\n";
echo "\n<!-- Кэш очистится через ".$time_clean." минуты -->\r\n";

}

?>