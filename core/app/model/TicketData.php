<?php
/**
 * Modelo para los tickets de soporte en Supportix.
 */
class TicketData {
	public static $tablename = "ticket";

	public $id;
	public $title;
	public $description;
	public $updated_at;
	public $created_at;
	public $kind_id;
	public $user_id;
	public $asigned_id;
	public $project_id;
	public $category_id;
	public $priority_id;
	public $status_id;

	// Cache de relaciones
	private ?ProjectData $_project = null;
	private ?PriorityData $_priority = null;
	private ?StatusData $_status = null;
	private ?KindData $_kind = null;
	private ?CategoryData $_category = null;
	private ?UserData $_user = null;

	public function __construct(){
		$this->title = "";
		$this->description = "";
		$this->kind_id = 1;
		$this->priority_id = 1;
		$this->status_id = 1;
	}

	private static function db(): \PDO {
		return Database::getPdo();
	}

	public function __get($name) {
		switch ($name) {
			case 'project':
				return $this->getProject();
			case 'priority':
				return $this->getPriority();
			case 'status':
				return $this->getStatus();
			case 'kind':
				return $this->getKind();
			case 'category':
				return $this->getCategory();
			case 'user':
				return $this->getUser();
		}
		return null;
	}

	public function getProject(): ?ProjectData {
		if ($this->_project === null && !empty($this->project_id)) {
			$this->_project = ProjectData::getById($this->project_id);
		}
		return $this->_project;
	}

	public function getPriority(): ?PriorityData {
		if ($this->_priority === null && !empty($this->priority_id)) {
			$this->_priority = PriorityData::getById($this->priority_id);
		}
		return $this->_priority;
	}

	public function getStatus(): ?StatusData {
		if ($this->_status === null && !empty($this->status_id)) {
			$this->_status = StatusData::getById($this->status_id);
		}
		return $this->_status;
	}

	public function getKind(): ?KindData {
		if ($this->_kind === null && !empty($this->kind_id)) {
			$this->_kind = KindData::getById($this->kind_id);
		}
		return $this->_kind;
	}

	public function getCategory(): ?CategoryData {
		if ($this->_category === null && !empty($this->category_id)) {
			$this->_category = CategoryData::getById($this->category_id);
		}
		return $this->_category;
	}

	public function getUser(): ?UserData {
		if ($this->_user === null && !empty($this->user_id)) {
			$this->_user = UserData::getById($this->user_id);
		}
		return $this->_user;
	}

	public function add(){
		$stmt = self::db()->prepare(
			"INSERT INTO ".self::$tablename." (title, description, category_id, project_id, priority_id, user_id, status_id, kind_id, created_at) " .
			"VALUES (:title, :description, :category_id, :project_id, :priority_id, :user_id, :status_id, :kind_id, NOW())"
		);
		$stmt->execute([
			'title'       => $this->title,
			'description' => $this->description,
			'category_id' => !empty($this->category_id) ? $this->category_id : null,
			'project_id'  => !empty($this->project_id) ? $this->project_id : null,
			'priority_id' => $this->priority_id ?: 1,
			'user_id'     => $this->user_id,
			'status_id'   => $this->status_id ?: 1,
			'kind_id'     => $this->kind_id ?: 1,
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
			"UPDATE ".self::$tablename." SET title = :title, description = :description, category_id = :category_id, " .
			"project_id = :project_id, priority_id = :priority_id, status_id = :status_id, kind_id = :kind_id, updated_at = NOW() WHERE id = :id"
		);
		$stmt->execute([
			'title'       => $this->title,
			'description' => $this->description,
			'category_id' => !empty($this->category_id) ? $this->category_id : null,
			'project_id'  => !empty($this->project_id) ? $this->project_id : null,
			'priority_id' => $this->priority_id ?: 1,
			'status_id'   => $this->status_id ?: 1,
			'kind_id'     => $this->kind_id ?: 1,
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
		$stmt = self::db()->query("SELECT * FROM ".self::$tablename." ORDER BY created_at DESC");
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function getAllPendings(){
		$stmt = self::db()->query("SELECT * FROM ".self::$tablename." WHERE status_id = 1 ORDER BY created_at DESC");
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function countPendings(): int {
		$stmt = self::db()->query("SELECT COUNT(*) AS total FROM ".self::$tablename." WHERE status_id = 1");
		$res = $stmt->fetch(\PDO::FETCH_ASSOC);
		return (int)($res['total'] ?? 0);
	}

	public static function getByFilter(array $filters = []): array {
		$sql = "SELECT * FROM " . self::$tablename . " WHERE 1=1";
		$params = [];

		if (!empty($filters['q'])) {
			$sql .= " AND (title LIKE :q OR description LIKE :q2)";
			$params['q'] = '%' . $filters['q'] . '%';
			$params['q2'] = '%' . $filters['q'] . '%';
		}

		if (!empty($filters['project_id'])) {
			$sql .= " AND project_id = :project_id";
			$params['project_id'] = (int)$filters['project_id'];
		}

		if (!empty($filters['category_id'])) {
			$sql .= " AND category_id = :category_id";
			$params['category_id'] = (int)$filters['category_id'];
		}

		if (!empty($filters['priority_id'])) {
			$sql .= " AND priority_id = :priority_id";
			$params['priority_id'] = (int)$filters['priority_id'];
		}

		if (!empty($filters['status_id'])) {
			$sql .= " AND status_id = :status_id";
			$params['status_id'] = (int)$filters['status_id'];
		}

		if (!empty($filters['kind_id'])) {
			$sql .= " AND kind_id = :kind_id";
			$params['kind_id'] = (int)$filters['kind_id'];
		}

		if (!empty($filters['date_at'])) {
			$sql .= " AND DATE(created_at) = :date_at";
			$params['date_at'] = $filters['date_at'];
		}

		if (!empty($filters['start_at']) && !empty($filters['finish_at'])) {
			$sql .= " AND DATE(created_at) >= :start_at AND DATE(created_at) <= :finish_at";
			$params['start_at'] = $filters['start_at'];
			$params['finish_at'] = $filters['finish_at'];
		} elseif (!empty($filters['start_at'])) {
			$sql .= " AND DATE(created_at) >= :start_at";
			$params['start_at'] = $filters['start_at'];
		} elseif (!empty($filters['finish_at'])) {
			$sql .= " AND DATE(created_at) <= :finish_at";
			$params['finish_at'] = $filters['finish_at'];
		}

		$sql .= " ORDER BY created_at DESC";

		$stmt = self::db()->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function getLatest(int $limit = 10): array {
		$stmt = self::db()->prepare("SELECT * FROM " . self::$tablename . " ORDER BY created_at DESC LIMIT :limit");
		$stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::class);
	}

	public static function getDailyCounts(int $days = 30): array {
		$sql = "SELECT DATE(created_at) AS date_val, COUNT(*) AS total 
				FROM " . self::$tablename . " 
				WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
				GROUP BY DATE(created_at)
				ORDER BY date_val ASC";
		$stmt = self::db()->prepare($sql);
		$stmt->bindValue(':days', $days - 1, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
	}

	public static function getCountsByStatus(): array {
		$sql = "SELECT s.id, s.name, COUNT(t.id) AS total
				FROM status s
				LEFT JOIN " . self::$tablename . " t ON t.status_id = s.id
				GROUP BY s.id, s.name
				ORDER BY s.id ASC";
		$stmt = self::db()->query($sql);
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public static function getCountsByCategory(): array {
		$sql = "SELECT c.id, c.name, COUNT(t.id) AS total
				FROM category c
				LEFT JOIN " . self::$tablename . " t ON t.category_id = c.id
				GROUP BY c.id, c.name
				HAVING total > 0
				ORDER BY total DESC";
		$stmt = self::db()->query($sql);
		$res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		if (empty($res)) {
			$sqlAll = "SELECT c.id, c.name, COUNT(t.id) AS total
					   FROM category c
					   LEFT JOIN " . self::$tablename . " t ON t.category_id = c.id
					   GROUP BY c.id, c.name
					   ORDER BY c.name ASC";
			$stmtAll = self::db()->query($sqlAll);
			$res = $stmtAll->fetchAll(\PDO::FETCH_ASSOC);
		}

		$stmtUncat = self::db()->query("SELECT COUNT(*) AS total FROM " . self::$tablename . " WHERE category_id IS NULL OR category_id = 0");
		$uncat = $stmtUncat->fetch(\PDO::FETCH_ASSOC);
		if (!empty($uncat['total']) && (int)$uncat['total'] > 0) {
			$res[] = [
				'id' => 0,
				'name' => 'Sin categoría',
				'total' => (int)$uncat['total']
			];
		}

		return $res;
	}
}

