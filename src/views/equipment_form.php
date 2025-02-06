<!DOCTYPE html>
<html>
<head>
    <title>IT Equipment Management - <?= isset($_GET['id']) ? 'Edit' : 'Add' ?> Equipment</title>
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
                <label for="type_id" class="form-label">Equipment Type</label>
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
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="country_id" class="form-label">Country</label>
                    <select name="country_id" id="country_id" class="form-control" required>
                        <option value="">Select Country</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?= $country['id'] ?>"><?= htmlspecialchars($country['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="branch_id" class="form-label">Branch</label>
                    <select name="branch_id" id="branch_id" class="form-control" required disabled>
                        <option value="">Select Branch</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select name="department_id" id="department_id" class="form-control" required disabled>
                        <option value="">Select Department</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="area_id" class="form-label">Area</label>
                    <select name="area_id" id="area_id" class="form-control" required disabled>
                        <option value="">Select Area</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Save Equipment</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script>
    // Location cascading dropdowns
    document.getElementById('country_id').addEventListener('change', function() {
        const countryId = this.value;
        const branchSelect = document.getElementById('branch_id');
        const departmentSelect = document.getElementById('department_id');
        const areaSelect = document.getElementById('area_id');
        
        // Reset and disable dependent dropdowns
        branchSelect.innerHTML = '<option value="">Select Branch</option>';
        departmentSelect.innerHTML = '<option value="">Select Department</option>';
        areaSelect.innerHTML = '<option value="">Select Area</option>';
        
        branchSelect.disabled = !countryId;
        departmentSelect.disabled = true;
        areaSelect.disabled = true;
        
        if (countryId) {
            fetch(`?action=get_branches&country_id=${countryId}`)
                .then(response => response.json())
                .then(branches => {
                    branches.forEach(branch => {
                        const option = document.createElement('option');
                        option.value = branch.id;
                        option.textContent = branch.name;
                        branchSelect.appendChild(option);
                    });
                });
        }
    });

    document.getElementById('branch_id').addEventListener('change', function() {
        const branchId = this.value;
        const departmentSelect = document.getElementById('department_id');
        const areaSelect = document.getElementById('area_id');
        
        departmentSelect.innerHTML = '<option value="">Select Department</option>';
        areaSelect.innerHTML = '<option value="">Select Area</option>';
        
        departmentSelect.disabled = !branchId;
        areaSelect.disabled = true;
        
        if (branchId) {
            fetch(`?action=get_departments&branch_id=${branchId}`)
                .then(response => response.json())
                .then(departments => {
                    departments.forEach(dept => {
                        const option = document.createElement('option');
                        option.value = dept.id;
                        option.textContent = dept.name;
                        departmentSelect.appendChild(option);
                    });
                });
        }
    });

    document.getElementById('department_id').addEventListener('change', function() {
        const departmentId = this.value;
        const areaSelect = document.getElementById('area_id');
        
        areaSelect.innerHTML = '<option value="">Select Area</option>';
        areaSelect.disabled = !departmentId;
        
        if (departmentId) {
            fetch(`?action=get_areas&department_id=${departmentId}`)
                .then(response => response.json())
                .then(areas => {
                    areas.forEach(area => {
                        const option = document.createElement('option');
                        option.value = area.id;
                        option.textContent = area.name;
                        areaSelect.appendChild(option);
                    });
                });
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