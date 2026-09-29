<?php
/**
 * Modelo para el catálogo de prioridades en Supportix.
 */
class PriorityData {
	public static $tablename = "priority";

	public $id;
	public $name;

	public function __construct(){
		$this->name = "";
	}

	private static function db(): \PDO {
		return Database::getPdo();
	}

	public function add(){
		$stmt = self::db()->prepare("INSERT INTO ".self::$tablename." (name) VALUES (:name)");
		$stmt->execute(['name' => $this->name]);
		$this->id = (int)self::db()->lastInsertId();
	}

	public static function delById($id){
		$stmt = self::db()->prepare("DELETE FROM ".self::$tablename." WHERE id = :id");
		$stmt->execute(['id' => $id]);
	}

	public function del(){
		self::delById($this->id);
	}

	public function update(){
		$stmt = self::db()->prepare("UPDATE ".self::$tablename." SET name = :name WHERE id = :id");
		$stmt->execute([
			'name' => $this->name,
			'id'   => $this->id,
		]);
	}

	public static function getById($id){
		$stmt = self::db()->prepare("SELECT * FROM ".self::$tablename." WHERE id = :id");
		$stmt->execute(['id' => $id]);
		$stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
		return $stmt->fetch() ?: null;
	}

	public static function getAll(){
		$stmt = self::db()->query("SELECT * FROM ".self::$tablename." ORDER BY id ASC");
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}
}
