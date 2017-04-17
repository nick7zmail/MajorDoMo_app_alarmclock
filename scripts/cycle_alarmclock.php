<?php

chdir(dirname(__FILE__) . '/../');

include_once("./config.php");
include_once("./lib/loader.php");
include_once("./lib/threads.php");
include_once(DIR_MODULES."app_alarmclock/app_alarmclock.class.php");
set_time_limit(0);

// connecting to database
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);

include_once("./load_settings.php");

$old_minute = date('i');
$old_hour = date('h');
if ($_GET['onetime']) {
 $old_minute = -1;
 if (date('i') == '00') {
  $old_hour = -1;
 }
}
$old_date = date('Y-m-d');

$checked_time = 0;
$started_time = time();

echo date("H:i:s") . " running " . basename(__FILE__) . "\n";

while (1)
{
	if (time() - $checked_time > 5)
	   {
		  setGlobal((str_replace('.php', '', basename(__FILE__))) . 'Run', time(), 1);
	   }
   $m = date('i');
   $h = date('h');
   $dt = date('Y-m-d');

   if ($m != $old_minute)
   {
		$app_alarmclock=new app_alarmclock();
		$app_alarmclock->check_alarm();
      $old_minute = $m;
   }

   if ($h != $old_hour)
   {
		//hour
   }

   if ($dt != $old_date)
   {
      //new day
      $old_date = $dt;
   }

   if (file_exists('./reboot') || IsSet($_GET['onetime']))
   {
      $db->Disconnect();
      exit;
   }
   sleep(1);
}

DebMes("Unexpected close of cycle: " . basename(__FILE__));
