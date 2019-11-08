<?php
	if($_REQUEST['action'] == 'quick' && isset($_REQUEST['area'])) {
		include("files3.php");
	} else {
		include("files2.php");
	}
?>