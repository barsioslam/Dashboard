<?php

namespace App\DB;

use App\Logger\Logger;

use \PDO; use \PDOException;

class Database {
	private $host;
	private $name;
	private $user;
	private $pass;
	private $connexion;
	
    private $lastStatement;

	private static $instance = null;
									
	function __construct($host = null, $name = null, $user = null, $pass = null){
		$config = parse_ini_file(CONF_PATH . "db.ini", true);
		if ($host != null) {
			$this->host = $host;
			$this->name = $name;
			$this->user = $user;
			$this->pass = $pass;
		} elseif ($config !== false)
			$this->host = $config["database"]["host"];
			$this->name = $config["database"]["name"];
			$this->user = $config["database"]["user"];
			$this->pass = $config["database"]["pass"];
		try{
			$this->connexion = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->name,
				$this->user, $this->pass, array(PDO::MYSQL_ATTR_INIT_COMMAND =>'SET NAMES UTF8', 
				PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
		}catch (PDOException $e){
			echo 'Erreur : Impossible de se connecter à la BDD ! <br><br>'.$e;
			die();
		}
	}
	
	// Singleton : récupération de l'instance unique
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new Database();
		}
		return self::$instance;
	}
	
	public function query($sql, $data = array()) {
		$this->lastStatement = $this->connexion->prepare($sql);
		$resultValue = $this->lastStatement->execute($data);
		if (!$resultValue) {
			Logger::error('SQL syntax: ' . $sql . '<br>SQL Error: '. self::getLastError());
		}
		return $resultValue;
	}
	
	public function prepare($sql, $data = array()) {
		$stmt = $this->connexion->prepare($sql);
		$resultValue = $stmt->execute($data);
		if (!$resultValue) {
			Logger::error('SQL syntax: ' . $sql . '<br>SQL Error: '. self::getLastError());
		}
		return $resultValue;
	}

	public function rowCount() : int {
		return $this->lastStatement ? $this->lastStatement->rowCount() : 0;
	}

	public function fetchAll() {
		if ($this->lastStatement) {
			$error = $this->lastStatement->errorInfo();
			if ($error[0] === "00000") {
				return $this->lastStatement->fetchAll(PDO::FETCH_ASSOC);
			}
			return [];
		}
		return [];
	}

	public function fetchAllColumns() {
		return $this->lastStatement->fetchAll(PDO::FETCH_COLUMN, 0);
	}

	public function getId() {
		return $this->connexion->lastInsertId();
	}

	public function getLastError(): ?string {
        if ($this->lastStatement) {
            $error = $this->lastStatement->errorInfo();
            return $error[2] ?? null; // message lisible
        }
        return null;
    }

}

?>