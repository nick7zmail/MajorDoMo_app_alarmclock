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
  $res=getObjectsByClass('AlarmClock');
  $total=count($res);
  if ($total) {
   //paging($res, 100, $out); // search result paging
   for($i=0;$i<$total;$i++) {
	$res[$i]['OBJ']=$res[$i]['TITLE'];
	$res[$i]['ALARMTIME']=gg($res[$i]['TITLE'].'.AlarmTime');
	$res[$i]['ALARMON']=gg($res[$i]['TITLE'].'.AlarmOn');
	$days=str_split(gg($res[$i]['TITLE'].'.days'));
	for($j=0;$j<7;$j++) {
		$res[$i]['DAY_'.$j]=$days[$j];
	}
	$res[$i]['NAME']=getObject($res[$i]['TITLE'])->description;
   }
   $out['RESULT']=$res;
  }
?>