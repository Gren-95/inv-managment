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

    public function getBySerial($serial) {
        $sql = "
            SELECT e.*, 
                   m.name as model_name, 
                   t.name as type_name,
                   u.name as user_name,
                   a.id as area_id,
                   a.name as area_name,
                   d.id as department_id,
                   d.name as department_name,
                   b.id as branch_id,
                   b.name as branch_name,
                   c.id as country_id,
                   c.name as country_name,
                   CONCAT(c.name, ' - ', b.name, ' - ', d.name, 
                          CASE WHEN a.name IS NOT NULL THEN CONCAT(' (', a.name, ')') ELSE '' END) as location
            FROM equipment e
            JOIN equipment_models m ON e.model_id = m.id
            JOIN equipment_types t ON m.type_id = t.id
            LEFT JOIN users u ON e.assigned_to_id = u.id
            LEFT JOIN areas a ON e.area_id = a.id
            LEFT JOIN departments d ON a.department_id = d.id
            LEFT JOIN branches b ON d.branch_id = b.id
            LEFT JOIN countries c ON b.country_id = c.id
            WHERE e.serial_number = ?";
            
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$serial]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function submitAudit($data) {
        $this->pdo->beginTransaction();
        try {
            // Insert audit record
            $sql = "INSERT INTO equipment_audits (
                equipment_id,
                serial_number,
                current_status,
                new_status,
                current_location_id,
                new_location_id,
                current_assigned_to_id,
                new_assigned_to_id,
                audit_notes,
                audited_by_user_id
            ) VALUES (
                :equipment_id,
                :serial_number,
                :current_status,
                :new_status,
                :current_location_id,
                :new_location_id,
                :current_assigned_to_id,
                :new_assigned_to_id,
                :audit_notes,
                :audited_by_user_id
            )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'equipment_id' => $data['equipment_id'],
                'serial_number' => $data['serial_number'],
                'current_status' => $data['current_status'],
                'new_status' => $data['new_status'],
                'current_location_id' => $data['current_location_id'],
                'new_location_id' => $data['new_location_id'],
                'current_assigned_to_id' => $data['current_assigned_to_id'],
                'new_assigned_to_id' => $data['new_assigned_to_id'],
                'audit_notes' => $data['audit_notes'],
                'audited_by_user_id' => 1 // TODO: Get from session
            ]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getAudits($fromDate, $toDate) {
        $sql = "SELECT a.*, 
                e.model_id,
                m.name as model_name,
                t.name as type_name,
                u1.name as audited_by_name,
                u2.name as approved_by_name,
                u3.name as current_assigned_to_name,
                u4.name as new_assigned_to_name,
                CASE 
                    WHEN a1.id IS NOT NULL THEN 
                        CONCAT(c1.name, ' - ', b1.name, ' - ', d1.name, ' - ', a1.name)
                    ELSE NULL 
                END as current_location,
                CASE 
                    WHEN a2.id IS NOT NULL THEN 
                        CONCAT(c2.name, ' - ', b2.name, ' - ', d2.name, ' - ', a2.name)
                    ELSE NULL 
                END as new_location
                FROM equipment_audits a
                JOIN equipment e ON a.equipment_id = e.id
                JOIN equipment_models m ON e.model_id = m.id
                JOIN equipment_types t ON m.type_id = t.id
                LEFT JOIN users u1 ON a.audited_by_user_id = u1.id
                LEFT JOIN users u2 ON a.approved_by_user_id = u2.id
                LEFT JOIN users u3 ON a.current_assigned_to_id = u3.id
                LEFT JOIN users u4 ON a.new_assigned_to_id = u4.id
                LEFT JOIN areas a1 ON a.current_location_id = a1.id
                LEFT JOIN departments d1 ON a1.department_id = d1.id
                LEFT JOIN branches b1 ON d1.branch_id = b1.id
                LEFT JOIN countries c1 ON b1.country_id = c1.id
                LEFT JOIN areas a2 ON a.new_location_id = a2.id
                LEFT JOIN departments d2 ON a2.department_id = d2.id
                LEFT JOIN branches b2 ON d2.branch_id = b2.id
                LEFT JOIN countries c2 ON b2.country_id = c2.id
                WHERE DATE(a.audit_date) BETWEEN :from_date AND :to_date
                ORDER BY a.audit_date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'from_date' => $fromDate,
            'to_date' => $toDate
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateAudits($ids, $action) {
        $this->pdo->beginTransaction();
        try {
            $status = $action === 'approve' ? 'approved' : 'rejected';
            
            // Update audit status
            $sql = "UPDATE equipment_audits 
                   SET status = :status,
                       approved_by_user_id = :user_id,
                       approval_date = CURRENT_TIMESTAMP
                   WHERE id IN (" . implode(',', array_map('intval', $ids)) . ")";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'status' => $status,
                'user_id' => 1 // TODO: Get from session
            ]);

            // If approved, update equipment
            if ($action === 'approve') {
                foreach ($ids as $id) {
                    $audit = $this->getAuditById($id);
                    
                    $updateSql = "UPDATE equipment 
                                SET status = :status,
                                    area_id = :area_id,
                                    assigned_to_id = :assigned_to_id,
                                    last_audit_date = CURRENT_TIMESTAMP,
                                    last_audited_by_id = :audited_by_id
                                WHERE id = :id";
                    
                    $updateStmt = $this->pdo->prepare($updateSql);
                    $updateStmt->execute([
                        'status' => $audit['new_status'],
                        'area_id' => $audit['new_location_id'],
                        'assigned_to_id' => $audit['new_assigned_to_id'],
                        'audited_by_id' => $audit['audited_by_user_id'],
                        'id' => $audit['equipment_id']
                    ]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function getAuditById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM equipment_audits WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
} 