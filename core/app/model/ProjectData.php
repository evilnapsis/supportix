<?php
/**
 * Modelo para la entidad Project en Supportix.
 */
class ProjectData {
	public static $tablename = "project";

	public $id;
	public $name;
	public $description;

	public function __construct(){
		$this->name = "";
		$this->description = "";
	}

	private static function db(): \PDO {
		return Database::getPdo();
	}

	public function add(){
		$stmt = self::db()->prepare("INSERT INTO ".self::$tablename." (name, description) VALUES (:name, :description)");
		$stmt->execute([
			'name'        => $this->name,
			'description' => $this->description,
		]);
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
		$stmt = self::db()->prepare("UPDATE ".self::$tablename." SET name = :name, description = :description WHERE id = :id");
		$stmt->execute([
			'name'        => $this->name,
			'description' => $this->description,
			'id'          => $this->id,
		]);
	}

	public static function getById($id){
		$stmt = self::db()->prepare("SELECT * FROM ".self::$tablename." WHERE id = :id");
		$stmt->execute(['id' => $id]);
		$stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
		return $stmt->fetch() ?: null;
	}

	public static function getAll(){
		$stmt = self::db()->query("SELECT * FROM ".self::$tablename." ORDER BY name ASC");
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function getLike($q){
		$stmt = self::db()->prepare("SELECT * FROM ".self::$tablename." WHERE name LIKE :q");
		$stmt->execute(['q' => '%'.$q.'%']);
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public function __get($prop) {
		if ($prop === 'tickets_count') {
			return $this->countTickets();
		}
		if ($prop === 'pending_tickets_count') {
			return $this->countPendingTickets();
		}
		return null;
	}

	public function countTickets(): int {
		$stmt = self::db()->prepare("SELECT COUNT(*) AS total FROM ticket WHERE project_id = :id");
		$stmt->execute(['id' => $this->id]);
		$res = $stmt->fetch(\PDO::FETCH_ASSOC);
		return (int)($res['total'] ?? 0);
	}

	public function countPendingTickets(): int {
		$stmt = self::db()->prepare("SELECT COUNT(*) AS total FROM ticket WHERE project_id = :id AND status_id = 1");
		$stmt->execute(['id' => $this->id]);
		$res = $stmt->fetch(\PDO::FETCH_ASSOC);
		return (int)($res['total'] ?? 0);
	}
}

