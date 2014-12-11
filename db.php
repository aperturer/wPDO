<?php
/**
 * Класс для работы с PDO-подключениями к базе.
 * Сначала вызываем add и сохраняем конфигурацию - одномерный массив 
 * с ключами конфигурации и опций. Потом получаем через get объект PDO. 
 * Благодаря отложенной загрузке, соединение создаётся только при его запросе.
 * Позволяет задавать неограниченное количество настроек подключений и получать 
 * нужное по имени, создавая соединение только когда оно запрошено.
 * Также есть простой режим, когда имя не указывается и используется первое из 
 * добавленных.
 * @author aperturer
 */
class db{
	private static $connects = array();
	private static $configs = array();
	private static $default_conf = array(
		'type'     => 'mysql',
		'charset'  => 'UTF8',
		'host'     => 'localhost',
		'dbname'   => 'database',
		'user'     => 'root',
		'password' => '',
	);
	private static $default_opts = array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	);

	/**
	 * Добавить (заменить) конфигурацию подключения к базе данных
	 * @param array $config массив с конфигурацией (см. default_conf + default_opts)
	 * @param string $name опциональное имя соединения
	 */
	public static function add($config, $name = ''){
		self::$configs[$name] = $config;
		unset(self::$connects[$name]); // чтобы заменить подключение если имя совпадает
	}

	/**
	 * Получить объект соединения по имени или первый из имеющихся.
	 * Объект PDO создаётся если ещё не был создан.
	 * @param string $name опциональное имя соединения
	 * @return object PDO
	 */
	public static function get($name = ''){
		if(!isset(self::$configs[$name])){
			if($name) throw new Exception("Error! No exist connection $name");
			if(!self::$configs) throw new Exception('Error! No connections!');
			list($name) = array_keys(self::$configs);
		}
		if(!isset(self::$connects[$name])){
			self::$connects[$name] = self::connect(self::$configs[$name]);
		}
		return self::$connects[$name];
	}

	/**
	 * Создаёт подключение используя конфигурацию.
	 * Не указанные параметры берутся из default_conf и default_opts
	 * @param array $config
	 * @return object PDO
	 */
	private static function connect($config){
		$options = array_merge(
			self::$default_opts,
			array_diff_key($config, self::$default_conf) // чего нет в default_conf - пойдёт в options
		);
		extract(array_merge( // безопасно - только ключи из default_conf
			self::$default_conf, 
			array_intersect_key($config, self::$default_conf)
		));
		$options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES '$charset'"; // надо для php < 5.4
		$dsn = "$type:host=$host;dbname=$dbname;charset=$charset";
		return new PDO($dsn, $user, $password, $options);
	}
}
