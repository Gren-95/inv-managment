<?php

class EquipmentType {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($data) {
        $sql = "INSERT INTO equipment_types (name, lifespan_years) VALUES (:name, :lifespan_years)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'lifespan_years' => $data['lifespan_years']
        ]);
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM equipment_types ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $sql = "DELETE FROM equipment_types WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function get($id) {
        $sql = "SELECT * FROM equipment_types WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $sql = "UPDATE equipment_types SET name = :name, lifespan_years = :lifespan_years WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'lifespan_years' => $data['lifespan_years']
        ]);
    }
} 