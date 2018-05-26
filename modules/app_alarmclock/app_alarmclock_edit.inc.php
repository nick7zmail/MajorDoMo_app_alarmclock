<?php
/*
* @version 0.1 (wizard)
*/
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $class='AlarmClock';
  $object_title=$id;
 
  if ($this->mode=='update') {
   $ok=1;
  //updating 'LANG_TITLE' (varchar, required)
	global $title;
	if ($title=='') {
		$out['ERR_TITLE']=1;
		$ok=0;
	}
	global $day_0;
	global $day_1;
	global $day_2;   
	global $day_3;   
	global $day_4;   
	global $day_5;   
	global $day_6;  
	global $custom_on;
	global $custom_off;	
	global $once;
	global $method;
	global $linked_object;
	global $linked_method;
	global $alarmon;
	global $code;
	global $alarmtime;
	if ($alarmtime=='') {
		$out['ERR_ALARMTIME']=1;
		$ok=0;
	}
	if(isset($day_0)) $days['DAY_0']=1; else $days['DAY_0']=0;
	if(isset($day_1)) $days['DAY_1']=1; else $days['DAY_1']=0;
	if(isset($day_2)) $days['DAY_2']=1; else $days['DAY_2']=0;
	if(isset($day_3)) $days['DAY_3']=1; else $days['DAY_3']=0;
	if(isset($day_4)) $days['DAY_4']=1; else $days['DAY_4']=0;
	if(isset($day_5)) $days['DAY_5']=1; else $days['DAY_5']=0;
	if(isset($day_6)) $days['DAY_6']=1; else $days['DAY_6']=0;

   
  //UPDATING RECORD
   if ($ok) {
    if ($id) {
	  $obj_id=addClassObject($class, $object_title); // adding new record
	  $object_rec=SQLSelectOne("SELECT * FROM objects WHERE ID=".$obj_id);
	  $object_rec['DESCRIPTION']=$title;
	  SQLUpdate('objects',$object_rec);
	  addLinkedProperty($object_title, 'AlarmOn', $this->name);
	  addLinkedProperty($object_title, 'AlarmTime', $this->name);
    } else {
     $new_rec=1;
	 $new_object_title='AlarmClock'.$this->getNewObjectIndex();
	  $obj_id=addClassObject($class, $new_object_title); // adding new record
	  $object_rec=SQLSelectOne("SELECT * FROM objects WHERE ID=".$obj_id);
	  $object_rec['DESCRIPTION']=$title;
	  SQLUpdate('objects',$object_rec);
	  sg($new_object_title.'.'.'AlarmOn', $alarmon);
	  sg($new_object_title.'.'.'AlarmTime', $alarmtime);
	  addLinkedProperty($new_object_title, 'AlarmOn', $this->name);
	  addLinkedProperty($new_object_title, 'AlarmTime', $this->name);
	  $id=$new_object_title;
	  $object_title=$id;
    }
		sg($object_title.'.days', implode('', $days));
		sg($object_title.'.custom_on', $custom_on);
		sg($object_title.'.custom_off', $custom_off);
		if(isset($once)) sg($object_title.'.once', 1); else sg($object_title.'.once', 0);
		sg($object_title.'.method', $method);
		if (IsSet($this->code)) {
			sg($object_title.'.code', $this->code);
		} else {
			sg($object_title.'.code', $code);
		}   
		sg($object_title.'.'.'linked_method', $linked_method);
		if(isset($alarmon)) sg($object_title.'.'.'AlarmOn', 1); else sg($object_title.'.'.'AlarmOn', 0);
		sg($object_title.'.'.'AlarmTime', $alarmtime);
		if ($method == 'method') {
			sg($object_title.'.'.'code', $linked_object.'.'.$linked_method);
		} elseif ($method == 'sound'){
			  $soundfiles=array();
			  $dir=ROOT.'/cms/sounds';
			  $handle = opendir( $dir );
			 while ( false !== $thing = readdir( $handle ) ) { 
			  if( $thing == '.' || $thing == '..' ) continue;
			  if (preg_match('/(.+?)\.mp3$/', $thing, $m))  {
			   $soundfiles[]=array('NAME'=>$m[1]);
			  }
			 }
			 $soundfiles_total=count($soundfiles);
			  for($i=0;$i<$soundfiles_total;$i++) {
			   if ($code == $soundfiles[$i]['NAME']) $soundfiles[$i]['SELECTED']=1;
			  }
			  closedir( $handle );
			  $out['SOUNDFILES']=$soundfiles;
			  $out['VAL']='Выберите звуковой файл:';
		 } elseif ($method == 'script'){ 
			$scripts=SQLSelect("SELECT TITLE FROM scripts");
			$soundfiles_total=count($scripts);
			  for($i=0;$i<$soundfiles_total;$i++) {
			   $soundfiles[$i]['NAME'] = $scripts[$i]['TITLE'];
			   if ($code == $soundfiles[$i]['NAME']) $soundfiles[$i]['SELECTED']=1;
			  }
			 $out['SOUNDFILES']=$soundfiles;
			 $out['VAL']='Выберите сценарий:';
		 } elseif ($method == 'code') {
			injectObjectMethodCode($object_title.'.AlarmRun', 'Alarmclock', $code);
			injectObjectMethodCode($object_title.'.AlarmRun.code', 'Alarmclock', $code);			 
		 }
    $out['OK']=1;
   } else {
    $out['ERR']=1;
   }
  }
 
  $obj=getObject($object_title); 
  $rec['TITLE']=$obj->description;
	$days=str_split(gg($object_title.'.days'));
	for($j=0;$j<7;$j++) {
		$rec['DAY_'.$j]=$days[$j];
	}
 
	$code=gg($object_title.'.code');
	 if (gg($object_title.'.method') == 'sound'){
		  $soundfiles=array();
		  $dir=ROOT.'/cms/sounds';
		  $handle = opendir( $dir );
		 while ( false !== $thing = readdir( $handle ) ) { 
		  if( $thing == '.' || $thing == '..' ) continue;
		  if (preg_match('/(.+?)\.mp3$/', $thing, $m))  {
		   $soundfiles[]=array('NAME'=>$m[1]);
		  }
		 }
		 $soundfiles_total=count($soundfiles);
		  for($i=0;$i<$soundfiles_total;$i++) {
		   if ($code == $soundfiles[$i]['NAME']) $soundfiles[$i]['SELECTED']=1;
		  }
		  closedir( $handle );
		  $out['SOUNDFILES']=$soundfiles;
		  $out['VAL']='Выберите звуковой файл:';
	 } elseif (gg($object_title.'.method') == 'script'){ 
		$scripts=SQLSelect("SELECT TITLE FROM scripts");
		$soundfiles_total=count($scripts);
		  for($i=0;$i<$soundfiles_total;$i++) {
		   $soundfiles[$i]['NAME'] = $scripts[$i]['TITLE'];
		   if ($code == $soundfiles[$i]['NAME']) $soundfiles[$i]['SELECTED']=1;
		  }
		 $out['SOUNDFILES']=$soundfiles;
		 $out['VAL']='Выберите сценарий:';
	 }
 
  if(gg($object_title.'.custom_on')!='') $rec['CUSTOM_ON']=gg($object_title.'.custom_on');
  if(gg($object_title.'.custom_off')!='') $rec['CUSTOM_OFF']=gg($object_title.'.custom_off');
  $rec['ONCE']=gg($object_title.'.once');
  if (gg($object_title.'.method') == 'method') {
	  $method_arr=explode('.', gg($object_title.'.code'));
	  $rec['LINKED_OBJECT']=$method_arr[0];
	  $rec['LINKED_METHOD']=$method_arr[1];
  } else {
	  $rec['CODE']=gg($object_title.'.code');
  }
  $rec['METHOD']=gg($object_title.'.method');
  $rec['CODE']=gg($object_title.'.code');
  $rec['ID']=$id;
  $rec['ALARMON']=gg($object_title.'.'.'AlarmOn');
  if(gg($object_title.'.AlarmTime')!='') $rec['ALARMTIME']=gg($object_title.'.'.'AlarmTime');
  
  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);
?>