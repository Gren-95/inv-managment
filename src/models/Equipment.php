<?php

class Equipment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($data) {
        $sql = "INSERT INTO equipment (
            model_id, buy_year, warranty_end,
            is_company_owned, status, assigned_to_id,
            serial_number,
            area_id
        ) VALUES (
            :model_id, :buy_year, :warranty_end,
            :is_company_owned, :status, :assigned_to_id,
            :serial_number,
            :area_id
        )";

        $stmt = $this->pdo->prepare($sql);
        // Convert checkbox value to boolean
        $data['is_company_owned'] = isset($data['is_company_owned']) ? 1 : 0;
        // Set default status if not provided
        if (empty($data['status'])) {
            $data['status'] = 'available';
        }
        // Handle empty assigned_to_id
        if (empty($data['assigned_to_id'])) {
            $data['assigned_to_id'] = null;
        }
        // Create clean data array with only needed fields
        $params = [
            'model_id' => $data['model_id'],
            'buy_year' => $data['buy_year'],
            'warranty_end' => $data['warranty_end'],
            'is_company_owned' => $data['is_company_owned'],
            'status' => $data['status'],
            'assigned_to_id' => $data['assigned_to_id'],
            'serial_number' => $data['serial_number'],
            'area_id' => $data['area_id']
        ];
        return $stmt->execute($params);
    }

    public function update($id, $data) {
        $oldData = $this->get($id);
        
        // If status is changing, record it
        if (isset($data['status']) && $data['status'] !== $oldData['status']) {
            $this->updateStatus(
                $id, 
                $data['status'],
                $data['changed_by_user_id'] ?? null,
                $data['status_comment'] ?? ''
            );
            // Remove status from data to prevent double update
            unset($data['status']);
        }

        // Continue with regular update
        $sql = "UPDATE equipment SET 
            status = :status,
            assigned_to_id = :assigned_to_id,
            country = :country,
            region = :region,
            department = :department,
            area = :area,
            is_company_owned = :is_company_owned,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = :id";

        // Convert checkbox value to boolean
        $data['is_company_owned'] = isset($data['is_company_owned']) ? 1 : 0;
        // Handle empty assigned_to_id
        if (empty($data['assigned_to_id'])) {
            $data['assigned_to_id'] = null;
        }
        // Create clean data array with only needed fields
        $params = [
            'id' => $id,
            'status' => $data['status'],
            'assigned_to_id' => $data['assigned_to_id'],
            'country' => $data['country'] ?? null,
            'region' => $data['region'] ?? null,
            'department' => $data['department'] ?? null,
            'area' => $data['area'] ?? null,
            'is_company_owned' => $data['is_company_owned']
        ];
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function writeOff($id) {
        $this->pdo->beginTransaction();
        try {
            // First create the write-off record
            $writeOffSql = "INSERT INTO write_offs (equipment_id, type, comment) 
                           VALUES (:equipment_id, :type, :comment)";
            $writeOffStmt = $this->pdo->prepare($writeOffSql);
            $writeOffStmt->execute([
                'equipment_id' => $id,
                'type' => $_POST['write_off_type'],
                'comment' => $_POST['write_off_comment']
            ]);

            // Then update the equipment status
            $sql = "UPDATE equipment SET 
                status = 'written_off',
                assigned_to_id = NULL,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getAll($filters = []) {
        $sql = "SELECT e.*, 
                m.name as model_name, 
                t.name as type_name,
                t.lifespan_years,
                TIMESTAMPDIFF(YEAR, STR_TO_DATE(CONCAT(e.buy_year, '-01-01'), '%Y-%m-%d'), CURDATE()) as age,
                u.name as user_name,
                c.name as country_name,
                b.name as branch_name,
                d.name as department_name,
                a.name as area_name
                FROM equipment e
                LEFT JOIN equipment_models m ON e.model_id = m.id
                LEFT JOIN equipment_types t ON m.type_id = t.id
                LEFT JOIN users u ON e.assigned_to_id = u.id
                LEFT JOIN areas a ON e.area_id = a.id
                LEFT JOIN departments d ON a.department_id = d.id
                LEFT JOIN branches b ON d.branch_id = b.id
                LEFT JOIN countries c ON b.country_id = c.id
                WHERE 1=1";

        $where = [];
        $params = [];

        if (!empty($filters['user_id'])) {
            $where[] = "e.assigned_to_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }

        if (!empty($filters['type_id'])) {
            $where[] = "t.id = :type_id";
            $params['type_id'] = $filters['type_id'];
        }

        if (!empty($filters['serial'])) {
            $where[] = "e.serial_number LIKE :serial";
            $params['serial'] = '%' . $filters['serial'] . '%';
        }

        if (!empty($where)) {
            $sql .= " AND " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY e.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllTypes() {
        $stmt = $this->pdo->query("SELECT * FROM equipment_types ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get($id) {
        $sql = "SELECT e.*, m.name as model_name, m.type_id, m.release_year,
                t.name as type_name, u.name as user_name 
                FROM equipment e
                JOIN equipment_models m ON e.model_id = m.id
                JOIN equipment_types t ON m.type_id = t.id
                LEFT JOIN users u ON e.assigned_to_id = u.id
                WHERE e.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getWriteOffHistory($id) {
        $sql = "SELECT * FROM write_offs WHERE equipment_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $newStatus, $userId = null, $comment = '') {
        $this->pdo->beginTransaction();
        try {
            // Get current status
            $sql = "SELECT status FROM equipment WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            $currentStatus = $stmt->fetchColumn();

            // Insert into history
            $historySQL = "INSERT INTO equipment_status_history 
                          (equipment_id, old_status, new_status, changed_by_user_id, comment)
                          VALUES (:equipment_id, :old_status, :new_status, :changed_by_user_id, :comment)";
            $historyStmt = $this->pdo->prepare($historySQL);
            $historyStmt->execute([
                'equipment_id' => $id,
                'old_status' => $currentStatus,
                'new_status' => $newStatus,
                'changed_by_user_id' => $userId,
                'comment' => $comment
            ]);

            // Update equipment status
            $updateSQL = "UPDATE equipment SET 
                         status = :status,
                         updated_at = CURRENT_TIMESTAMP
                         WHERE id = :id";
            $updateStmt = $this->pdo->prepare($updateSQL);
            $updateStmt->execute([
                'id' => $id,
                'status' => $newStatus
            ]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getStatusHistory($id) {
        $sql = "SELECT 
                h.*,
                u.name as changed_by_name
                FROM equipment_status_history h
                LEFT JOIN users u ON h.changed_by_user_id = u.id
                WHERE h.equipment_id = :id
                ORDER BY h.changed_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllStatusHistory() {
        $sql = "SELECT 
                h.*,
                e.serial_number,
                m.name as model_name,
                t.name as type_name,
                u.name as changed_by_name,
                u2.name as assigned_to_name
                FROM equipment_status_history h
                JOIN equipment e ON h.equipment_id = e.id
                JOIN equipment_models m ON e.model_id = m.id
                JOIN equipment_types t ON m.type_id = t.id
                LEFT JOIN users u ON h.changed_by_user_id = u.id
                LEFT JOIN users u2 ON e.assigned_to_id = u2.id
                ORDER BY h.changed_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 