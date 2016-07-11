<?php
/*
* @version 0.1 (wizard)
*/
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $table_name='app_alarmclock';
  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
  $out['ALARMTIME']=gg($rec['LINKED_OBJECT'].'.'.'AlarmTime');
  $out['ALARMON']=gg($rec['LINKED_OBJECT'].'.'.'AlarmOn');
  if ($this->mode=='update') {
   $ok=1;
  //updating 'LANG_TITLE' (varchar, required)
   global $title;
   $rec['TITLE']=$title;
   if ($rec['TITLE']=='') {
    $out['ERR_TITLE']=1;
    $ok=0;
   }
  $out['ALARMTIME']=gg($rec['LINKED_OBJECT'].'.'.'AlarmTime');
  $out['ALARMON']=gg($rec['LINKED_OBJECT'].'.'.'AlarmOn');
  //updating 'day_0' (varchar)
   global $day_0;
   if(isset($day_0)) $rec['DAY_0']=1; else $rec['DAY_0']=0;
  //updating 'day_1' (varchar)
   global $day_1;
   if(isset($day_1)) $rec['DAY_1']=1; else $rec['DAY_1']=0;
  //updating 'day_2' (varchar)
   global $day_2;
   if(isset($day_2)) $rec['DAY_2']=1; else $rec['DAY_2']=0;
  //updating 'day_3' (varchar)
   global $day_3;
   if(isset($day_3)) $rec['DAY_3']=1; else $rec['DAY_3']=0;
  //updating 'day_4' (varchar)
   global $day_4;
   if(isset($day_4)) $rec['DAY_4']=1; else $rec['DAY_4']=0;
  //updating 'day_5' (varchar)
   global $day_5;
   if(isset($day_5)) $rec['DAY_5']=1; else $rec['DAY_5']=0;
  //updating 'day_6' (varchar)
   global $day_6;
   if(isset($day_6)) $rec['DAY_6']=1; else $rec['DAY_6']=0;
  //updating 'custom_on' (varchar)
   global $custom_on;
   $rec['CUSTOM_ON']=$custom_on;
  //updating 'custom_off' (varchar)
   global $custom_off;
   $rec['CUSTOM_OFF']=$custom_off;
  //updating 'custom_off' (varchar)
   global $once;
   if(isset($once)) $rec['ONCE']=1; else $rec['ONCE']=0;
  //updating 'LANG_LINKED_OBJECT' (varchar)
   global $linked_object;
   $rec['LINKED_OBJECT']=$linked_object;
   if ($rec['LINKED_OBJECT']=='') {
    $out['ERR_LINKED_OBJECT']=1;
    $ok=0;
   }
  //updating 'LANG_LINKED_PROPERTY' (varchar)
   global $linked_property;
   $rec['LINKED_PROPERTY']=$linked_property;
  //updating LANG_METHOD (varchar)
   global $linked_method;
   $rec['LINKED_METHOD']=$linked_method;
  //updating 'AlarmOn' (varchar)
   global $alarmon;
   if(isset($alarmon)) sg($rec['LINKED_OBJECT'].'.'.'AlarmOn', 1); else sg($rec['LINKED_OBJECT'].'.'.'AlarmOn', 0);
   //sg($rec['LINKED_OBJECT'].'.'.'AlarmOn', $alarmon);
  //updating 'AlarmTime' (varchar)
   global $alarmtime;
   sg($rec['LINKED_OBJECT'].'.'.'AlarmTime', $alarmtime);
  //UPDATING RECORD
   if ($ok) {
    if ($rec['ID']) {
     SQLUpdate($table_name, $rec); // update
	 $out['ALARMTIME']=gg($rec['LINKED_OBJECT'].'.'.'AlarmTime');
     $out['ALARMON']=gg($rec['LINKED_OBJECT'].'.'.'AlarmOn');
    } else {
     $new_rec=1;
     $rec['ID']=SQLInsert($table_name, $rec); // adding new record
	  sg($rec['LINKED_OBJECT'].'.'.'AlarmOn', $alarmon);
	  sg($rec['LINKED_OBJECT'].'.'.'AlarmTime', $alarmtime);
	  addLinkedProperty($rec['LINKED_OBJECT'], 'AlarmOn', $this->name);
	  addLinkedProperty($rec['LINKED_OBJECT'], 'AlarmTime', $this->name);
	  $out['ALARMTIME']=$alarmtime;
	  $out['ALARMON']=$alarmon;
    }
    $out['OK']=1;
   } else {
    $out['ERR']=1;
   }
  }
  
  
  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);
