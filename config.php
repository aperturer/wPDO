<?php
// Задаём настройки подключения
db::add(array(
	'dbname'             => 'databasename', 
	'password'           => 'password',
	PDO::ATTR_PERSISTENT => true,
));
