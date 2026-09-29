<?php
/**
 * Modelo de usuarios del sistema Supportix.
 */
class UserData {
	public static $tablename = "user";

	public $id;
	public $username;
	public $name;
	public $lastname;
	public $email;
	public $password;
	public $is_active;
	public $kind; // 1: Administrador, 2: Usuario normal
	public $created_at;
	public $is_admin;

	public function __construct(){
		$this->username = "";
		$this->name = "";
		$this->lastname = "";
		$this->email = "";
		$this->password = "";
		$this->is_active = 1;
		$this->kind = 2;
		$this->created_at = "NOW()";
	}

	private static function db(): \PDO {
		return Database::getPdo();
	}

	public function __get($prop) {
		if ($prop === 'is_admin') {
			return (int)$this->kind === 1;
		}
		return null;
	}

	public function isAdmin(): bool {
		return (int)$this->kind === 1;
	}

	public function getFullName(): string {
		$full = trim(($this->name ?? '') . ' ' . ($this->lastname ?? ''));
		return !empty($full) ? $full : ($this->username ?? 'Usuario');
	}

	public function add(){
		$stmt = self::db()->prepare(
			"INSERT INTO ".self::$tablename." (username, name, lastname, email, password, kind, is_active, created_at) " .
			"VALUES (:username, :name, :lastname, :email, :password, :kind, :is_active, NOW())"
		);
		$stmt->execute([
			'username'  => $this->username,
			'name'      => $this->name,
			'lastname'  => $this->lastname,
			'email'     => $this->email,
			'password'  => $this->password,
			'kind'      => $this->kind ?: 2,
			'is_active' => isset($this->is_active) ? (int)$this->is_active : 1,
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
		$stmt = self::db()->prepare(
			"UPDATE ".self::$tablename." SET username = :username, name = :name, lastname = :lastname, " .
			"email = :email, kind = :kind, is_active = :is_active WHERE id = :id"
		);
		$stmt->execute([
			'username'  => $this->username,
			'name'      => $this->name,
			'lastname'  => $this->lastname,
			'email'     => $this->email,
			'kind'      => $this->kind ?: 2,
			'is_active' => isset($this->is_active) ? (int)$this->is_active : 1,
			'id'        => $this->id,
		]);
	}

	public function update_passwd(){
		$stmt = self::db()->prepare("UPDATE ".self::$tablename." SET password = :password WHERE id = :id");
		$stmt->execute([
			'password' => $this->password,
			'id'       => $this->id
		]);
	}

	public static function getById($id){
		$stmt = self::db()->prepare("SELECT * FROM ".self::$tablename." WHERE id = :id");
		$stmt->execute(['id' => $id]);
		$stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
		$user = $stmt->fetch() ?: null;
		if ($user) {
			$user->is_admin = ((int)$user->kind === 1);
		}
		return $user;
	}

	public static function getByUsernameOrEmail($login){
		$stmt = self::db()->prepare("SELECT * FROM ".self::$tablename." WHERE (username = :login OR email = :login) LIMIT 1");
		$stmt->execute(['login' => $login]);
		$stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
		$user = $stmt->fetch() ?: null;
		if ($user) {
			$user->is_admin = ((int)$user->kind === 1);
		}
		return $user;
	}

	public static function getByMail($mail){
		$stmt = self::db()->prepare("SELECT * FROM ".self::$tablename." WHERE email = :email");
		$stmt->execute(['email' => $mail]);
		$stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
		$user = $stmt->fetch() ?: null;
		if ($user) {
			$user->is_admin = ((int)$user->kind === 1);
		}
		return $user;
	}

	public static function getAll(){
		$stmt = self::db()->query("SELECT * FROM ".self::$tablename." ORDER BY id DESC");
		$users = $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
		foreach ($users as $user) {
			$user->is_admin = ((int)$user->kind === 1);
		}
		return $users;
	}

	public static function getLike($q){
		$stmt = self::db()->prepare("SELECT * FROM ".self::$tablename." WHERE name LIKE :q OR lastname LIKE :q OR username LIKE :q");
		$stmt->execute(['q' => '%'.$q.'%']);
		$users = $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
		foreach ($users as $user) {
			$user->is_admin = ((int)$user->kind === 1);
		}
		return $users;
	}
}
