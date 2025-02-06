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

    public function hasPermission($userId, $permissionName) {
        $sql = "SELECT COUNT(*) as count 
                FROM user_permissions up 
                JOIN permissions p ON up.permission_id = p.id 
                WHERE up.user_id = ? AND p.name = ? AND 
                EXISTS (SELECT 1 FROM users u WHERE u.id = up.user_id AND u.active = TRUE)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $permissionName]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function authenticate($username, $password) {
        $sql = "SELECT id, password FROM users WHERE ppid = ? AND active = TRUE";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If password is empty, user needs to set it up
        if ($user && empty($user['password'])) {
            return ['needs_setup' => true, 'id' => $user['id']];
        }

        if ($user && password_verify($password, $user['password'])) {
            return ['id' => $user['id']];
        }
        return false;
    }

    public function setPassword($userId, $password) {
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            password_hash($password, PASSWORD_DEFAULT),
            $userId
        ]);
    }

    public function checkPPID($ppid) {
        $sql = "SELECT id FROM users WHERE ppid = ? AND active = TRUE";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ppid]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllPermissions() {
        $stmt = $this->pdo->query("SELECT * FROM permissions ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserPermissions($userId) {
        $sql = "SELECT permission_id FROM user_permissions WHERE user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'permission_id');
    }

    public function updatePermissions($userId, $permissions) {
        // Prevent modifying your own permissions
        if ($userId == $_SESSION['user_id']) {
            return false;
        }
        $this->pdo->beginTransaction();
        try {
            // Delete existing permissions
            $sql = "DELETE FROM user_permissions WHERE user_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);

            // Insert new permissions
            if (!empty($permissions)) {
                $sql = "INSERT INTO user_permissions (user_id, permission_id) VALUES (?, ?)";
                $stmt = $this->pdo->prepare($sql);
                foreach ($permissions as $permId) {
                    $stmt->execute([$userId, $permId]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function toggleActive($userId, $active) {
        // Prevent deactivating your own account
        if ($userId == $_SESSION['user_id']) {
            return false;
        }
        $sql = "UPDATE users SET active = :active WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':active' => $active ? 1 : 0,
            ':id' => $userId
        ]);
    }
} 