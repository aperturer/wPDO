<?php
require_once 'db.php';
require_once 'config.php';

// Получаем коннект к базе
$pdo = db::get(); 
// Делаем запрос
$res = $pdo->query("SELECT name FROM foo"); 
// Получаем все строки в массив и выводим его содержимое
echo implode(' ', $res->fetchAll(PDO::FETCH_COLUMN, 0)); 
