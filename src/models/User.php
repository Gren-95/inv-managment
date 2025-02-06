<?php

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($data) {
        $sql = "INSERT INTO users (name, email) VALUES (:name, :email)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email']
        ]);
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $sql = "UPDATE users SET name = :name, email = :email WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email']
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function importFromCSV($file) {
        $results = [
            'success' => 0,
            'errors' => []
        ];

        if (($handle = fopen($file, "r")) !== FALSE) {
            // Skip header row
            $header = fgetcsv($handle);

            // Prepare insert statement
            $sql = "INSERT INTO users (name, email, ppid) VALUES (:name, :email, :ppid)
                    ON DUPLICATE KEY UPDATE 
                    name = VALUES(name),
                    ppid = VALUES(ppid)";
            $stmt = $this->pdo->prepare($sql);

            while (($data = fgetcsv($handle)) !== FALSE) {
                // Skip empty rows
                if (empty($data[0]) && empty($data[1]) && empty($data[2])) {
                    continue;
                }

                try {
                    $stmt->execute([
                        'name' => trim($data[0]),
                        'email' => trim($data[1]),
                        'ppid' => trim($data[2])
                    ]);
                    $results['success']++;
                } catch (PDOException $e) {
                    $results['errors'][] = "Error importing user {$data[0]}: " . $e->getMessage();
                }
            }
            fclose($handle);
        }

        return $results;
    }
} 