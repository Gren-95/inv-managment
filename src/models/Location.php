<?php

class Location {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Countries
    public function createCountry($data) {
        $sql = "INSERT INTO countries (name) VALUES (:name)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['name' => $data['name']]);
    }

    public function getAllCountries() {
        $stmt = $this->pdo->query("SELECT * FROM countries ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateCountry($id, $data) {
        $sql = "UPDATE countries SET name = :name WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name']
        ]);
    }

    public function deleteCountry($id) {
        $sql = "DELETE FROM countries WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Branches
    public function createBranch($data) {
        $sql = "INSERT INTO branches (country_id, name) VALUES (:country_id, :name)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'country_id' => $data['country_id'],
            'name' => $data['name']
        ]);
    }

    public function getAllBranches() {
        $sql = "SELECT b.*, c.name as country_name 
                FROM branches b 
                JOIN countries c ON b.country_id = c.id 
                ORDER BY c.name, b.name";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBranchesByCountry($country_id) {
        $sql = "SELECT * FROM branches WHERE country_id = :country_id ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['country_id' => $country_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateBranch($id, $data) {
        $sql = "UPDATE branches SET country_id = :country_id, name = :name WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'country_id' => $data['country_id'],
            'name' => $data['name']
        ]);
    }

    public function deleteBranch($id) {
        $sql = "DELETE FROM branches WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Departments
    public function createDepartment($data) {
        $sql = "INSERT INTO departments (branch_id, name) VALUES (:branch_id, :name)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'branch_id' => $data['branch_id'],
            'name' => $data['name']
        ]);
    }

    public function getAllDepartments() {
        $sql = "SELECT d.*, b.name as branch_name, c.name as country_name 
                FROM departments d
                JOIN branches b ON d.branch_id = b.id
                JOIN countries c ON b.country_id = c.id
                ORDER BY c.name, b.name, d.name";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDepartmentsByBranch($branch_id) {
        $sql = "SELECT * FROM departments WHERE branch_id = :branch_id ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['branch_id' => $branch_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateDepartment($id, $data) {
        $sql = "UPDATE departments SET branch_id = :branch_id, name = :name WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'branch_id' => $data['branch_id'],
            'name' => $data['name']
        ]);
    }

    public function deleteDepartment($id) {
        $sql = "DELETE FROM departments WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Areas
    public function createArea($data) {
        $sql = "INSERT INTO areas (department_id, name) VALUES (:department_id, :name)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'department_id' => $data['department_id'],
            'name' => $data['name']
        ]);
    }

    public function getAllAreas() {
        $sql = "SELECT a.*, d.name as department_name, b.name as branch_name, c.name as country_name 
                FROM areas a
                JOIN departments d ON a.department_id = d.id
                JOIN branches b ON d.branch_id = b.id
                JOIN countries c ON b.country_id = c.id
                ORDER BY c.name, b.name, d.name, a.name";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAreasByDepartment($department_id) {
        $sql = "SELECT * FROM areas WHERE department_id = :department_id ORDER BY name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['department_id' => $department_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateArea($id, $data) {
        $sql = "UPDATE areas SET department_id = :department_id, name = :name WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'department_id' => $data['department_id'],
            'name' => $data['name']
        ]);
    }

    public function deleteArea($id) {
        $sql = "DELETE FROM areas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Get full location details
    public function getFullLocationPath($area_id) {
        $sql = "SELECT 
                a.name as area_name,
                d.name as department_name,
                b.name as branch_name,
                c.name as country_name
                FROM areas a
                JOIN departments d ON a.department_id = d.id
                JOIN branches b ON d.branch_id = b.id
                JOIN countries c ON b.country_id = c.id
                WHERE a.id = :area_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['area_id' => $area_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAreaDetails($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM areas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDepartmentDetails($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBranchDetails($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM branches WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
} 