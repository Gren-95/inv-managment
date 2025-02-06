<?php
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

// Basic routing
$action = $_GET['action'] ?? 'list';

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
        if (isset($_GET['import'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
                $results = $user->importFromCSV($_FILES['csv_file']['tmp_name']);
            }
            include 'views/import_users.php';
            break;
        }
        $edit_user = null;
        if (isset($_GET['edit_id'])) {
            $edit_user = $user->get($_GET['edit_id']);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['delete_user'])) {
                $user->delete($_POST['id']);
            } else if (isset($_POST['id'])) {
                $user->update($_POST['id'], $_POST);
            } else {
                $user->create($_POST);
            }
            header('Location: index.php?action=users');
            exit;
        }
        $users = $user->getAll();
        include 'views/users.php';
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
            include 'views/account_label.php';
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