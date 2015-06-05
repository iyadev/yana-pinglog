<?php
if(isset($_GET) && isset($_GET['from']) && isset($_GET['log']) && isset($_POST) && isset($_POST['content'])  )
{
	$cmd =  __DIR__."/log-distant/pc_".$_GET['from']."_".$_GET['log'].".log";
	$cmd = "echo '".str_replace("'",'"', base64_decode($_POST['content']))."' > ".$cmd;
	echo $cmd;
	system($cmd);
	echo "Updated";
}
