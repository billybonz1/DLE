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

echo "\n<!-- ��� ����� ".$after_cache." ������ -->\r\n";
echo "\n<!-- ��� ��������� ����� ".$time_clean." ������ -->\r\n";

}

?>