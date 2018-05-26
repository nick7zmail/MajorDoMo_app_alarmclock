<?php
$class_name='AlarmClock';
$class_id=addClass($class_name);
if ($class_id) {
	$class=SQLSelectOne("SELECT * FROM classes WHERE ID=".$class_id);
	$class['DESCRIPTION']='Будильники';
	SQLUpdate('classes',$class);
}
$this->render_prop('AlarmOn', 'Включен/Выключен');
$this->render_prop('AlarmTime', 'Время');
$this->render_prop('method', 'Метод оповещения');
$this->render_prop('once', 'Одноразовый');
$this->render_prop('custom_on', 'Надпись при включенном');
$this->render_prop('custom_off', 'Надпись при выключенном');
$this->render_prop('days', 'Дни недели');
$this->render_prop('value', 'Надпись (для вставки в меню)');

$method_id=addClassMethod($class_name, 'AlarmRun');
if ($method_id) {
	$class=SQLSelectOne("SELECT * FROM methods WHERE ID=".$method_id);
	$class['DESCRIPTION']='Вызывается при срабатывании';
	SQLUpdate('methods',$class);
}
?>
