<?php

class EquipmentModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($data) {
        $sql = "INSERT INTO equipment_models (type_id, name, release_year) 
                VALUES (:type_id, :name, :release_year)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'type_id' => $data['type_id'],
            'name' => $data['name'],
            'release_year' => $data['release_year']
        ]);
    }

    public function getAll() {
        $sql = "SELECT m.*, t.name as type_name 
                FROM equipment_models m 
                JOIN equipment_types t ON m.type_id = t.id 
                ORDER BY t.name, m.name";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByType($type_id) {
        $sql = "SELECT * FROM equipment_models WHERE type_id = :type_id ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['type_id' => $type_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $sql = "DELETE FROM equipment_models WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function get($id) {
        $sql = "SELECT m.*, t.name as type_name 
                FROM equipment_models m 
                JOIN equipment_types t ON m.type_id = t.id 
                WHERE m.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $sql = "UPDATE equipment_models SET 
                name = :name,
                type_id = :type_id,
                release_year = :release_year 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }
} 