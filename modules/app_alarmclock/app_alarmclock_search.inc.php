<?php
/*
* @version 0.1 (wizard)
*/
 global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $qry="1";
  // search filters
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['app_alarmclock_qry'];
  } else {
   $session->data['app_alarmclock_qry']=$qry;
  }
  if (!$qry) $qry="1";
  $sortby_app_alarmclock="ID DESC";
  $out['SORTBY']=$sortby_app_alarmclock;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM app_alarmclock WHERE $qry ORDER BY ".$sortby_app_alarmclock);
  if ($res[0]['ID']) {
   //paging($res, 100, $out); // search result paging
   $total=count($res);
   for($i=0;$i<$total;$i++) {
	$res[$i]['ALARMTIME']=gg($res[$i]['LINKED_OBJECT'].'.'.'AlarmTime');
	$res[$i]['ALARMON']=gg($res[$i]['LINKED_OBJECT'].'.'.'AlarmOn');
   }
   $out['RESULT']=$res;
  }
