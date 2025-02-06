<!DOCTYPE html>
<html>
<head>
    <title><?= isset($_GET['id']) ? 'Edit' : 'Add' ?> Equipment - ITEM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1><?= isset($_GET['id']) ? 'Edit' : 'Add' ?> Equipment</h1>
        <form method="POST" class="mt-3">
            <?php if (isset($_GET['id'])): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($_GET['id']) ?>">
            <?php endif; ?>

            <?php if (!isset($item['status'])): ?>
                <input type="hidden" name="status" value="available">
            <?php endif; ?>

            <div class="mb-3">
                <label for="type_id" class="form-label">Type</label>
                <select name="type_id" id="type_id" class="form-control" required>
                    <option value="">Select Type</option>
                    <?php 
                    $types = $equipment->getAllTypes();
                    foreach ($types as $type): 
                        $selected = ($item && $item['type_id'] == $type['id']) ? 'selected' : '';
                    ?>
                        <option value="<?= $type['id'] ?>" <?= $selected ?>><?= htmlspecialchars($type['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="model_id" class="form-label">Model</label>
                <select name="model_id" id="model_id" class="form-control" required>
                    <option value="">Select Model</option>
                    <?php if ($item): ?>
                        <option value="<?= $item['model_id'] ?>" selected><?= htmlspecialchars($item['model_name']) ?></option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="buy_year" class="form-label">Buy Year</label>
                    <input type="number" class="form-control" id="buy_year" name="buy_year" 
                           value="<?= $item ? htmlspecialchars($item['buy_year']) : date('Y') ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="warranty_end" class="form-label">Warranty End</label>
                    <input type="date" class="form-control" id="warranty_end" name="warranty_end" 
                           value="<?= $item ? htmlspecialchars($item['warranty_end']) : '' ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="serial_number" class="form-label">Serial Number</label>
                <input type="text" class="form-control" id="serial_number" name="serial_number" 
                       value="<?= $item ? htmlspecialchars($item['serial_number']) : '' ?>" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">TeamViewer ID</label>
                    <input type="number" class="form-control" name="teamviewer_id" 
                           value="<?= $item ? htmlspecialchars($item['teamviewer_id']) : '' ?>"
                           min="0" step="1">
                </div>
                <div class="col-md-6">
                    <label class="form-label">CERF ID</label>
                    <input type="number" class="form-control" name="cerf_id" 
                           value="<?= $item ? htmlspecialchars($item['cerf_id']) : '' ?>"
                           min="0" step="1">
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_company_owned" name="is_company_owned" value="1" 
                           <?= (!$item || $item['is_company_owned']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_company_owned">Company Owned</label>
                </div>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <?php 
                    $statuses = ['available', 'assigned', 'maintenance', 'written_off'];
                    foreach ($statuses as $status):
                        $selected = ($item && $item['status'] == $status) ? 'selected' : '';
                    ?>
                        <option value="<?= $status ?>" <?= $selected ?>><?= ucfirst($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3" id="statusCommentDiv" style="display: none;">
                <label for="status_comment" class="form-label">Status Change Comment</label>
                <textarea name="status_comment" id="status_comment" class="form-control" rows="2"></textarea>
            </div>

            <div class="mb-3">
                <label for="assigned_to_id" class="form-label">Assigned To</label>
                <select name="assigned_to_id" id="assigned_to_id" class="form-control">
                    <option value="">Not Assigned</option>
                    <?php foreach ($users as $user): 
                        $selected = ($item && $item['assigned_to_id'] == $user['id']) ? 'selected' : '';
                    ?>
                        <option value="<?= $user['id'] ?>" <?= $selected ?>><?= htmlspecialchars($user['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Location Selection -->
            <div class="mb-3">
                <label class="form-label">Location</label>
                <div class="row g-2">
                    <div class="col">
                        <select class="form-control" id="country_id" name="country_id" required>
                            <option value="">Select Country</option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?= $country['id'] ?>"
                                   <?= ($item && $item['country_id'] == $country['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($country['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col">
                        <select class="form-control" id="branch_id" name="branch_id" required disabled>
                            <option value="">Select Branch</option>
                            <?php if ($item && isset($item['branch_id']) && isset($item['branch_name'])): ?>
                                <option value="<?= $item['branch_id'] ?>" selected>
                                    <?= htmlspecialchars($item['branch_name']) ?>
                                </option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col">
                        <select class="form-control" id="department_id" name="department_id" required disabled>
                            <option value="">Select Department</option>
                            <?php if ($item && isset($item['department_id']) && isset($item['department_name'])): ?>
                                <option value="<?= $item['department_id'] ?>" selected>
                                    <?= htmlspecialchars($item['department_name']) ?>
                                </option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col">
                        <select class="form-control" id="area_id" name="area_id" required disabled>
                            <option value="">Select Area</option>
                            <?php if ($item && isset($item['area_id']) && isset($item['area_name'])): ?>
                                <option value="<?= $item['area_id'] ?>" selected>
                                    <?= htmlspecialchars($item['area_name']) ?>
                                </option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script>
    // Function to initialize location dropdowns
    async function initializeLocationDropdowns() {
        <?php if ($item): ?>
            // Trigger country change to load branches
            await loadBranches(<?= json_encode($item['country_id']) ?>);
            
            // Trigger branch change to load departments
            await loadDepartments(<?= json_encode($item['branch_id']) ?>);
            
            // Trigger department change to load areas
            await loadAreas(<?= json_encode($item['department_id']) ?>);
        <?php endif; ?>
    }

    // Function to load branches
    async function loadBranches(countryId) {
        if (!countryId) return;
        
        try {
            const response = await fetch(`?action=get_branches&country_id=${countryId}`);
            const branches = await response.json();
            
            const branchSelect = document.getElementById('branch_id');
            branchSelect.innerHTML = '<option value="">Select Branch</option>';
            
            branches.forEach(branch => {
                const option = document.createElement('option');
                option.value = branch.id;
                option.textContent = branch.name;
                if (<?= $item ? $item['branch_id'] : 'null' ?> == branch.id) {
                    option.selected = true;
                }
                branchSelect.appendChild(option);
            });
            
            branchSelect.disabled = false;
            if (branchSelect.value) {
                await loadDepartments(branchSelect.value);
            }
        } catch (error) {
            console.error('Failed to fetch branches:', error);
        }
    }

    // Function to load departments
    async function loadDepartments(branchId) {
        if (!branchId) return;
        
        try {
            const response = await fetch(`?action=get_departments&branch_id=${branchId}`);
            const departments = await response.json();
            
            const departmentSelect = document.getElementById('department_id');
            departmentSelect.innerHTML = '<option value="">Select Department</option>';
            
            departments.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.id;
                option.textContent = dept.name;
                if (<?= $item ? $item['department_id'] : 'null' ?> == dept.id) {
                    option.selected = true;
                }
                departmentSelect.appendChild(option);
            });
            
            departmentSelect.disabled = false;
            if (departmentSelect.value) {
                await loadAreas(departmentSelect.value);
            }
        } catch (error) {
            console.error('Failed to fetch departments:', error);
        }
    }

    // Function to load areas
    async function loadAreas(departmentId) {
        if (!departmentId) return;
        
        try {
            const response = await fetch(`?action=get_areas&department_id=${departmentId}`);
            const areas = await response.json();
            
            const areaSelect = document.getElementById('area_id');
            areaSelect.innerHTML = '<option value="">Select Area</option>';
            
            areas.forEach(area => {
                const option = document.createElement('option');
                option.value = area.id;
                option.textContent = area.name;
                if (<?= $item ? $item['area_id'] : 'null' ?> == area.id) {
                    option.selected = true;
                }
                areaSelect.appendChild(option);
            });
            
            areaSelect.disabled = false;
        } catch (error) {
            console.error('Failed to fetch areas:', error);
        }
    }

    // Location cascade selects
    document.getElementById('country_id').addEventListener('change', async function() {
        const departmentSelect = document.getElementById('department_id');
        const areaSelect = document.getElementById('area_id');
        
        departmentSelect.innerHTML = '<option value="">Select Department</option>';
        areaSelect.innerHTML = '<option value="">Select Area</option>';
        
        await loadBranches(this.value);
        departmentSelect.disabled = true;
        areaSelect.disabled = true;
    });

    document.getElementById('branch_id').addEventListener('change', async function() {
        const areaSelect = document.getElementById('area_id');
        
        areaSelect.innerHTML = '<option value="">Select Area</option>';
        
        await loadDepartments(this.value);
        areaSelect.disabled = true;
    });

    document.getElementById('department_id').addEventListener('change', async function() {
        await loadAreas(this.value);
    });

    // Initialize dropdowns when editing
    document.addEventListener('DOMContentLoaded', async function() {
        const countrySelect = document.getElementById('country_id');
        if (countrySelect.value) {
            await loadBranches(countrySelect.value);
        }
    });

    // Add JavaScript to handle dynamic model loading based on type selection
    document.getElementById('type_id').addEventListener('change', function() {
        const typeId = this.value;
        const modelSelect = document.getElementById('model_id');
        
        // Clear current options
        modelSelect.innerHTML = '<option value="">Select Model</option>';
        
        if (typeId) {
            // Fetch models for selected type
            fetch(`?action=get_models&type_id=${typeId}`)
                .then(response => response.json())
                .then(models => {
                    models.forEach(model => {
                        const option = document.createElement('option');
                        option.value = model.id;
                        option.textContent = model.name;
                        modelSelect.appendChild(option);
                    });
                });
        }
    });

    // Show comment field when status changes
    document.getElementById('status').addEventListener('change', function() {
        const currentStatus = '<?= $item['status'] ?? '' ?>';
        const commentDiv = document.getElementById('statusCommentDiv');
        if (this.value !== currentStatus) {
            commentDiv.style.display = 'block';
        } else {
            commentDiv.style.display = 'none';
        }
    });
    </script>
</body>
</html> 