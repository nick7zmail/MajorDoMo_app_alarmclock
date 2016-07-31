<?php
/**
* Р‘СѓРґРёР»СЊРЅРёРє 
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 21:06:15 [Jun 21, 2016])
*/
//
//
class app_alarmclock extends module {
/**
* app_alarmclock
*
* Module class constructor
*
* @access private
*/
function app_alarmclock() {
  $this->name="app_alarmclock";
  $this->title="Будильник";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='app_alarmclock' || $this->data_source=='') {
  if ($this->view_mode=='' || $this->view_mode=='search_app_alarmclock') {
   $this->search_app_alarmclock($out);
  }
  if ($this->view_mode=='edit_app_alarmclock') {
   $this->edit_app_alarmclock($out, $this->id);
  }
  if ($this->view_mode=='delete_app_alarmclock') {
   $this->delete_app_alarmclock($this->id);
   $this->redirect("?");
  }
 }
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* app_alarmclock search
*
* @access public
*/
 function search_app_alarmclock(&$out) {
  require(DIR_MODULES.$this->name.'/app_alarmclock_search.inc.php');
 }
/**
* app_alarmclock edit/add
*
* @access public
*/
 function edit_app_alarmclock(&$out, $id) {
  require(DIR_MODULES.$this->name.'/app_alarmclock_edit.inc.php');
 }
/**
* app_alarmclock delete record
*
* @access public
*/
 function delete_app_alarmclock($id) {
  $rec=SQLSelectOne("SELECT * FROM app_alarmclock WHERE ID='$id'");
  removeLinkedProperty($rec['LINKED_OBJECT'], 'AlarmOn', $this->name);
  removeLinkedProperty($rec['LINKED_OBJECT'], 'AlarmTime', $this->name);
  SQLExec("DELETE FROM app_alarmclock WHERE ID='".$rec['ID']."'");
 }
 function propertySetHandle($object, $property, $value) {
   $table='app_alarmclock';
   $properties=SQLSelect("SELECT * FROM $table WHERE LINKED_OBJECT LIKE '".DBSafe($object)."'");
    DebMes("AlarmClock: изменено свойство будильника $object.$property");
   $total=count($properties);
   if ($total) {
    for($i=0;$i<$total;$i++) {
     //to-do
	 if (gg($properties[$i]['LINKED_OBJECT'].'.'.'AlarmOn') == 1 ) {
		if (stripos($properties[$i]['CUSTOM_ON'], '%TIME%') !== false) {
			$val=str_replace('%TIME%', gg($properties[$i]['LINKED_OBJECT'].'.'.'AlarmTime'), $properties[$i]['CUSTOM_ON']);
		} else {$val=$properties[$i]['CUSTOM_ON'];}	
	 } else {
		if (stripos($properties[$i]['CUSTOM_OFF'], '%TIME%') !== false) {
			$val=str_replace('%TIME%', gg($properties[$i]['LINKED_OBJECT'].'.'.'AlarmTime'), $properties[$i]['CUSTOM_OFF']);
		} else {$val=$properties[$i]['CUSTOM_OFF'];}
	 }
	 sg($properties[$i]['LINKED_OBJECT'].'.'.'value', $val);
    }
   }
 }
 
 function check_alarm() {
 $this->getConfig();
 $db_rec=SQLSelect("SELECT * FROM app_alarmclock");
	 for ($i = 1; $i <= count($db_rec); $i++) {
		$rec=$db_rec[$i-1];
		if (gg($rec['LINKED_OBJECT'].'.AlarmTime') == date('H:i')) {
		$dow='DAY_'.(date('N')-1);
			if ($rec[$dow]==1) {
				if (gg($rec['LINKED_OBJECT'].'.AlarmOn') == 1) {
					DebMes("AlarmClock: сработал будильник ".$rec['TITLE']);
					cm($rec['LINKED_OBJECT'].'.'.$rec['LINKED_METHOD']);
					if ($rec['ONCE'] == 1) {
						sg($rec['LINKED_OBJECT'].'.AlarmOn', 0);
					}
				}
			}
		}
	 }
 }
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS app_alarmclock');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall() {
/*
app_alarmclock - 
*/
  $data = <<<EOD
 app_alarmclock: ID int(10) unsigned NOT NULL auto_increment
 app_alarmclock: TITLE varchar(100) NOT NULL DEFAULT ''
 app_alarmclock: DAY_0 varchar(100) NOT NULL DEFAULT ''
 app_alarmclock: DAY_1 varchar(100) NOT NULL DEFAULT ''
 app_alarmclock: DAY_2 varchar(100) NOT NULL DEFAULT ''
 app_alarmclock: DAY_3 varchar(100) NOT NULL DEFAULT ''
 app_alarmclock: DAY_4 varchar(100) NOT NULL DEFAULT ''
 app_alarmclock: DAY_5 varchar(100) NOT NULL DEFAULT ''
 app_alarmclock: DAY_6 varchar(100) NOT NULL DEFAULT ''
 app_alarmclock: CUSTOM_ON varchar(255) NOT NULL DEFAULT ''
 app_alarmclock: CUSTOM_OFF varchar(255) NOT NULL DEFAULT ''
 app_alarmclock: ONCE varchar(100) NOT NULL DEFAULT ''
 app_alarmclock: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 app_alarmclock: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
 app_alarmclock: LINKED_METHOD varchar(100) NOT NULL DEFAULT ''
EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgSnVuIDIxLCAyMDE2IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
