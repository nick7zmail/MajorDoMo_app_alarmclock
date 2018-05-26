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
	removeLinkedProperty($id, 'AlarmOn', $this->name);
	removeLinkedProperty($id, 'AlarmTime', $this->name);
	deleteObject($id);
 }
 function propertySetHandle($object, $property, $value) {
    //DebMes("AlarmClock: изменено свойство будильника $object.$property");

     //to-do
	 if (gg($object.'.AlarmOn') == 1 ) {
		if (stripos(gg($object.'.custom_on'), '%TIME%') !== false) {
			$val=str_replace('%TIME%', gg($object.'.AlarmTime'), gg($object.'.custom_on'));
		} else {$val=gg($object.'.custom_on');}	
	 } else {
		if (stripos(gg($object.'.custom_off'), '%TIME%') !== false) {
			$val=str_replace('%TIME%', gg($object.'.AlarmTime'), gg($object.'.custom_off'));
		} else {$val=gg($object.'.custom_off');}
	 }
	 sg($object.'.value', $val);
 }
 
 function getNewObjectIndex() {
    $objects=getObjectsByClass('AlarmClock');
    $index=0;
    $total = count($objects);
    for ($i = 0; $i < $total; $i++) {
        if (preg_match('/(\d+)/',$objects[$i]['TITLE'],$m)) {
            $current_index=(int)$m[1];
            if ($current_index>$index) {
                $index=$current_index;
            }
        }
    }
    $index++;
    if ($index<10) {
        $index='0'.$index;
    }
    return $index;
}
 
 function check_alarm() {
 $this->getConfig();	
 $db_rec=getObjectsByClass('AlarmClock');
	 for ($i = 0; $i < count($db_rec); $i++) {
		$rec=$db_rec[$i];
		if (gg($rec['TITLE'].'.AlarmTime') == date('H:i')) {
		$days=str_split(gg($rec['TITLE'].'.days'));
			if ($days[(date('N')-1)]==1) {
				if (gg($rec['TITLE'].'.AlarmOn') == 1) {
					//DebMes("AlarmClock: сработал будильник ".$rec['TITLE']);
					$m=gg($rec['TITLE'].'.method');
					if ($m == 'sound') {
						PlaySound(gg($rec['TITLE'].'.code'));
					} elseif ($m == 'method') {
						cm(gg($rec['TITLE'].'.code'));
					} elseif ($m == 'script'){
						rs(gg($rec['TITLE'].'.code'));
					} else {
						cm($m.'.AlarmRun');
					}
					if (gg($rec['TITLE'].'.once') == 1) {
						sg($rec['TITLE'].'.AlarmOn', 0);
					}
				}
			}
		}
	 }
 }
 
 function render_structure() {
  require(DIR_MODULES.$this->name.'/app_alarmclock_structure.inc.php');
 }
 
 function render_prop($prop, $desc) {
	$class_name='AlarmClock';
	$prop_id=addClassProperty($class_name, $prop);
	if ($prop_id) {
		$prop=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
		$prop['DESCRIPTION']=$desc;
		SQLUpdate('properties',$prop);
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
  $this->render_structure();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/

// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgSnVuIDIxLCAyMDE2IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
?>