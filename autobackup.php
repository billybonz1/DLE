<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2004,2011 SoftNews Media Group
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: autobackup.php
-----------------------------------------------------
 Назначение: Автоматический бекап базы данных
=====================================================
*/

if( !defined( 'E_DEPRECATED' ) ) {

	@error_reporting ( E_ALL ^ E_NOTICE );
	@ini_set ( 'error_reporting', E_ALL ^ E_NOTICE );

} else {

	@error_reporting ( E_ALL ^ E_DEPRECATED ^ E_NOTICE );
	@ini_set ( 'error_reporting', E_ALL ^ E_DEPRECATED ^ E_NOTICE );

}

@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Внимание: В целях безопасности мы рекомендуем переименовать файл
autobackup.php в любое другое название с расширением PHP

Для работы автобекапа необходима поддержка вашим хостингом
запуска приложений с использованием Cron более подробную
информацию о том как использовать данную функцию вы можете
получить у вашего хостинг провайдера.
 ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Для включения поддержки автоматического бекапа БД вы должны 
поставить значение 1 для переменной $allow_auto_backup
 ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

$allow_auto_backup = 0;

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Укажите какое количество файлов с резервной копией БД 
хранить на сервере
 ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

$max_count_files = 5;

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Не редактируйте код который следует ниже.
 ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

	if ($allow_auto_backup) {

		define('DATALIFEENGINE', true);
		define('AUTOMODE', true);

		define('ROOT_DIR', dirname (__FILE__));
		define('ENGINE_DIR', ROOT_DIR.'/engine');
		require_once ENGINE_DIR.'/classes/mysql.php';
		require_once ENGINE_DIR.'/data/dbconfig.php';

		$files = array();

		if (is_dir(ROOT_DIR.'/backup/') && $handle = opendir(ROOT_DIR.'/backup/')) {
            while (false !== ($file = readdir($handle))) {
                if (preg_match("/^.+?\.sql(\.(gz|bz2))?$/", $file)) {

				$prefix = explode("_", $file);
				$prefix = end($prefix);
				$prefix = explode(".", $prefix);
				$prefix = reset($prefix);


				if (strlen($prefix) == 32)
                    $files[] = $file;

                }
            }
            closedir($handle);
        }

        sort($files);
		reset($files);

		if (count($files) >= $max_count_files) {
			@unlink (ROOT_DIR.'/backup/'.$files[0]);
		}

		$member_id = array();
		$member_id['user_group'] = 1;
		$_REQUEST['action'] = "backup";
		$_POST['comp_method'] = 1;

		include_once ROOT_DIR.'/engine/inc/dumper.php';

		echo ("done"); die ();
	}

		echo ("MySQL Backup not allowed"); die ();
?>