<?php
session_start();

require_once 'config/database.php';
require_once 'models/Equipment.php';
require_once 'models/EquipmentType.php';
require_once 'models/EquipmentModel.php';
require_once 'models/User.php';
require_once 'models/Location.php';
require_once 'models/SharedAccount.php';

$equipment = new Equipment($pdo);
$equipmentType = new EquipmentType($pdo);
$equipmentModel = new EquipmentModel($pdo);
$user = new User($pdo);
$location = new Location($pdo);
$sharedAccount = new SharedAccount($pdo);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Handle PPID check for password setup
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_ppid'])) {
        $userExists = $user->checkPPID($_POST['check_ppid']);
        if ($userExists) {
            $ppid = $_POST['check_ppid'];
            include 'views/setup_password.php';
            exit;
        } else {
            $error = 'Invalid PPID';
            include 'views/setup_password.php';
            exit;
        }
    }

    // Handle password setup
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password']) && isset($_POST['confirm_password'])) {
        if ($_POST['password'] !== $_POST['confirm_password']) {
            $error = 'Passwords do not match';
            $ppid = $_POST['ppid'];
            include 'views/setup_password.php';
            exit;
        }
        
        if (strlen($_POST['password']) < 8) {
            $error = 'Password must be at least 8 characters long';
            $ppid = $_POST['ppid'];
            include 'views/setup_password.php';
            exit;
        }
        
        $userId = $user->authenticate($_POST['ppid'], '');
        if ($userId && isset($userId['needs_setup'])) {
            $user->setPassword($userId['id'], $_POST['password']);
            $_SESSION['user_id'] = $userId['id'];
            header('Location: index.php');
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
        $result = $user->authenticate($_POST['username'], $_POST['password']);
        if ($result === false) {
            $error = 'Invalid PPID or password';
            include 'views/login.php';
            exit;
        }

        if (isset($result['needs_setup'])) {
            $ppid = $_POST['username'];
            include 'views/setup_password.php';
            exit;
        } else {
            if ($user->hasPermission($result['id'], 'login')) {
                $_SESSION['user_id'] = $result['id'];
                header('Location: index.php');
                exit;
            }
            $error = 'Access denied';
            include 'views/login.php';
            exit;
        }
    }
    include 'views/login.php';
    exit;
}

// Basic routing
$action = $_GET['action'] ?? 'list';

// Permission mapping
$requiredPermissions = [
    'list' => 'view_equipment',
    'create' => 'manage_equipment',
    'update' => 'manage_equipment',
    'write_off' => 'write_off_equipment',
    'users' => 'manage_users',
    'locations' => 'manage_locations',
    'models_and_types' => 'manage_models',
    'audit' => 'perform_audit',
    'audit_review' => 'approve_audit',
    'shared_accounts' => 'manage_shared_accounts'
];

// Check permission for current action
if (isset($requiredPermissions[$action]) && 
    !$user->hasPermission($_SESSION['user_id'], $requiredPermissions[$action])) {
    die('Access Denied');
}

switch ($action) {
    case 'create':
        $item = null;
        $users = $user->getAll();
        $countries = $location->getAllCountries();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $equipment->create($_POST);
            header('Location: index.php');
            exit;
        }
        include 'views/equipment_form.php';
        break;
    
    case 'update':
        $item = null;
        if (isset($_GET['id'])) {
            $item = $equipment->get($_GET['id']);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $equipment->update($_POST['id'], $_POST);
            header('Location: index.php');
            exit;
        }
        $users = $user->getAll();
        $countries = $location->getAllCountries();
        include 'views/equipment_form.php';
        break;
    
    case 'write_off':
        $item = null;
        if (isset($_GET['id'])) {
            $item = $equipment->get($_GET['id']);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $equipment->writeOff($_POST['id']);
            header('Location: index.php');
            exit;
        }
        include 'views/write_off_form.php';
        break;

    case 'models_and_types':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['delete_type'])) {
                $equipmentType->delete($_POST['id']);
            } else if (isset($_POST['delete_model'])) {
                $equipmentModel->delete($_POST['id']);
            } else if (isset($_POST['id']) && isset($_POST['type_id'])) {
                $equipmentModel->update($_POST['id'], $_POST);
            } else if (isset($_POST['id'])) {
                if (isset($_POST['type_id'])) {
                    $equipmentModel->create($_POST);
                } else {
                    $equipmentType->create($_POST);
                }
            }
            header('Location: index.php?action=models_and_types');
            exit;
        }
        $types = $equipmentType->getAll();
        $models = $equipmentModel->getAll();
        include 'views/equipment_models_and_types.php';
        break;

    case 'users':
        // Get all permissions for the modal
        $permissions = $user->getAllPermissions();
        
        if (isset($_GET['import'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
                $results = $user->importFromCSV($_FILES['csv_file']['tmp_name']);
            }
            include 'views/import_users.php';
            break;
        }
        $users = $user->getAll();
        include 'views/users.php';
        break;

    case 'update_user_permissions':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $success = $user->updatePermissions(
                $_POST['user_id'], 
                $_POST['permissions'] ?? []
            );
            echo json_encode(['success' => $success]);
            exit;
        }
        break;

    case 'reset_user_password':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            if ($_POST['password'] === $_POST['confirm_password']) {
                $success = $user->setPassword(
                    $_POST['user_id'],
                    $_POST['password']
                );
                echo json_encode(['success' => $success]);
            } else {
                echo json_encode(['error' => 'Passwords do not match']);
            }
            exit;
        }
        break;

    case 'toggle_user_active':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['user_id']) || !isset($data['active'])) {
                echo json_encode(['error' => 'Missing required fields']);
                exit;
            }
            $success = $user->toggleActive($data['user_id'], $data['active']);
            echo json_encode(['success' => $success]);
            exit;
        }
        break;

    case 'get_user_permissions':
        if (isset($_GET['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode($user->getUserPermissions($_GET['user_id']));
            exit;
        }
        break;

    case 'get_models':
        if (isset($_GET['type_id'])) {
            $models = $equipmentModel->getAllByType($_GET['type_id']);
            header('Content-Type: application/json');
            echo json_encode($models);
            exit;
        }
        break;

    case 'get_branches':
        if (isset($_GET['country_id'])) {
            $branches = $location->getBranchesByCountry($_GET['country_id']);
            header('Content-Type: application/json');
            echo json_encode($branches);
            exit;
        }
        break;

    case 'get_departments':
        if (isset($_GET['branch_id'])) {
            $departments = $location->getDepartmentsByBranch($_GET['branch_id']);
            header('Content-Type: application/json');
            echo json_encode($departments);
            exit;
        }
        break;

    case 'get_areas':
        if (isset($_GET['department_id'])) {
            $areas = $location->getAreasByDepartment($_GET['department_id']);
            header('Content-Type: application/json');
            echo json_encode($areas);
            exit;
        }
        break;

    case 'get_area_details':
        if (isset($_GET['area_id'])) {
            $area = $location->getAreaDetails($_GET['area_id']);
            header('Content-Type: application/json');
            echo json_encode($area);
            exit;
        }
        break;

    case 'get_department_details':
        if (isset($_GET['department_id'])) {
            $dept = $location->getDepartmentDetails($_GET['department_id']);
            header('Content-Type: application/json');
            echo json_encode($dept);
            exit;
        }
        break;

    case 'get_branch_details':
        if (isset($_GET['branch_id'])) {
            $branch = $location->getBranchDetails($_GET['branch_id']);
            header('Content-Type: application/json');
            echo json_encode($branch);
            exit;
        }
        break;

    case 'locations':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['type'])) {
                switch ($_POST['type']) {
                    case 'country':
                        if (isset($_POST['delete'])) {
                            $location->deleteCountry($_POST['id']);
                        } else {
                            $location->createCountry($_POST);
                        }
                        break;
                    case 'branch':
                        if (isset($_POST['delete'])) {
                            $location->deleteBranch($_POST['id']);
                        } else {
                            $location->createBranch($_POST);
                        }
                        break;
                    case 'department':
                        if (isset($_POST['delete'])) {
                            $location->deleteDepartment($_POST['id']);
                        } else {
                            $location->createDepartment($_POST);
                        }
                        break;
                    case 'area':
                        if (isset($_POST['delete'])) {
                            $location->deleteArea($_POST['id']);
                        } else {
                            $location->createArea($_POST);
                        }
                        break;
                }
            }
            header('Location: index.php?action=locations');
            exit;
        }

        $countries = $location->getAllCountries();
        $branches = $location->getAllBranches();
        $departments = $location->getAllDepartments();
        $areas = $location->getAllAreas();
        include 'views/locations.php';
        break;

    case 'status_history':
        if (isset($_GET['id'])) {
            $item = $equipment->get($_GET['id']);
            $history = $equipment->getStatusHistory($_GET['id']);
            include 'views/status_history.php';
        } else {
            header('Location: index.php');
            exit;
        }
        break;

    case 'equipment_log':
        $history = $equipment->getAllStatusHistory();
        include 'views/equipment_log.php';
        break;

    case 'audit':
        $users = $user->getAll();
        $countries = $location->getAllCountries();
        include 'views/equipment_audit.php';
        break;

    case 'audit_review':
        $fromDate = $_GET['from_date'] ?? date('Y-m-d', strtotime('-7 days'));
        $toDate = $_GET['to_date'] ?? date('Y-m-d');
        $audits = $equipment->getAudits($fromDate, $toDate);
        include 'views/audit_review.php';
        break;

    case 'update_audits':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            header('Content-Type: application/json');
            try {
                $equipment->updateAudits($data['ids'], $data['action']);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
        break;

    case 'print_label':
        if (isset($_GET['id'])) {
            $item = $equipment->get($_GET['id']);
            include 'views/equipment_label.php';
        } else {
            header('Location: index.php');
            exit;
        }
        break;

    case 'shared_accounts':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['delete_account'])) {
                $sharedAccount->delete($_POST['id']);
            } else if (isset($_POST['id'])) {
                $sharedAccount->update($_POST['id'], $_POST);
            } else {
                $sharedAccount->create($_POST);
            }
            header('Location: index.php?action=shared_accounts');
            exit;
        }
        $accounts = $sharedAccount->getAll();
        include 'views/shared_accounts.php';
        break;

    case 'print_account_label':
        if (isset($_GET['id'])) {
            $account = $sharedAccount->get($_GET['id']);
            include 'views/shared_account_label.php';
        } else {
            header('Location: index.php?action=shared_accounts');
            exit;
        }
        break;

    case 'api_get_equipment':
        if (isset($_GET['serial'])) {
            header('Content-Type: application/json');
            $data = $equipment->getBySerial($_GET['serial']);
            if ($data) {
                echo json_encode($data);
            } else {
                echo json_encode(['error' => 'Equipment not found']);
            }
            exit;
        }
        break;

    case 'api_submit_audit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            try {
                $equipment->submitAudit($_POST);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['error' => 'Failed to submit audit']);
            }
            exit;
        }
        break;

    case 'about':
        include 'views/about.php';
        break;

    case 'delete_location':
        // ... existing delete code ...
        break;
        
    case 'edit_location':
        $type = $_POST['type'];
        $id = $_POST['id'];
        $name = $_POST['name'];
        
        try {
            switch ($type) {
                case 'country':
                    $sql = "UPDATE countries SET name = ? WHERE id = ?";
                    $params = [$name, $id];
                    break;
                    
                case 'branch':
                    $sql = "UPDATE branches SET name = ?, country_id = ? WHERE id = ?";
                    $params = [$name, $_POST['parent_id'], $id];
                    break;
                    
                case 'department':
                    $sql = "UPDATE departments SET name = ?, branch_id = ? WHERE id = ?";
                    $params = [$name, $_POST['parent_id'], $id];
                    break;
                    
                case 'area':
                    $sql = "UPDATE areas SET name = ?, department_id = ? WHERE id = ?";
                    $params = [$name, $_POST['parent_id'], $id];
                    break;
                    
                default:
                    throw new Exception("Invalid location type");
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;

    default:
        $filters = [
            'user_id' => $_GET['user_id'] ?? null,
            'type_id' => $_GET['type_id'] ?? null,
            'serial' => $_GET['serial'] ?? null
        ];
        $items = $equipment->getAll($filters);
        $users = $user->getAll();
        $types = $equipmentType->getAll();
        include 'views/equipment_list.php';
        break;
} 