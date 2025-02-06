<!DOCTYPE html>
<html>
<head>
    <title>Equipment Audit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Equipment Audit</h1>
            <div>
                <a href="?action=audit_review" class="btn btn-secondary">Review Audits</a>
                <a href="index.php" class="btn btn-secondary">Back to List</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Scan Equipment</h5>
                <div class="mb-3">
                    <label for="serial" class="form-label">Serial Number</label>
                    <input type="text" class="form-control" id="serial" autofocus>
                </div>
            </div>
        </div>

        <div id="auditForm" class="card d-none">
            <div class="card-body">
                <h5 class="card-title">Current Equipment Information</h5>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Model</dt>
                            <dd class="col-sm-8" id="currentModel">-</dd>
                            
                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8" id="currentStatus">-</dd>
                            
                            <dt class="col-sm-4">Assigned To</dt>
                            <dd class="col-sm-8" id="currentAssigned">-</dd>
                            
                            <dt class="col-sm-4">Location</dt>
                            <dd class="col-sm-8" id="currentLocation">-</dd>
                        </dl>
                    </div>
                </div>

                <h5 class="card-title">Update Information</h5>
                <form id="updateForm">
                    <input type="hidden" id="equipmentId" name="equipment_id">
                    <input type="hidden" id="serialNumber" name="serial_number">
                    <input type="hidden" id="currentStatus" name="current_status">
                    <input type="hidden" id="currentLocationId" name="current_location_id">
                    <input type="hidden" id="currentAssignedToId" name="current_assigned_to_id">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="new_status" required>
                                <option value="available">Available</option>
                                <option value="assigned">Assigned</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Assigned To</label>
                            <select class="form-select" name="new_assigned_to_id">
                                <option value="">Not Assigned</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>">
                                        <?= htmlspecialchars($user['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Location</label>
                            <div class="row g-2">
                                <div class="col">
                                    <select class="form-select" id="country" required>
                                        <option value="">Select Country</option>
                                        <?php foreach ($countries as $country): ?>
                                            <option value="<?= $country['id'] ?>">
                                                <?= htmlspecialchars($country['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <select class="form-select" id="branch" disabled required>
                                        <option value="">Select Branch</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select class="form-select" id="department" disabled required>
                                        <option value="">Select Department</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select class="form-select" id="area" name="new_location_id" disabled required>
                                        <option value="">Select Area</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="audit_notes" rows="3"></textarea>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit Audit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="errorMessage" class="alert alert-danger d-none mt-3"></div>
    </div>

    <script>
        // Location cascade selects
        const countrySelect = document.getElementById('country');
        const branchSelect = document.getElementById('branch');
        const departmentSelect = document.getElementById('department');
        const areaSelect = document.getElementById('area');

        countrySelect.addEventListener('change', async function() {
            branchSelect.innerHTML = '<option value="">Select Branch</option>';
            departmentSelect.innerHTML = '<option value="">Select Department</option>';
            areaSelect.innerHTML = '<option value="">Select Area</option>';
            
            if (!this.value) {
                branchSelect.disabled = true;
                departmentSelect.disabled = true;
                areaSelect.disabled = true;
                return;
            }

            try {
                const response = await fetch(`index.php?action=get_branches&country_id=${this.value}`);
                const branches = await response.json();
                
                branches.forEach(branch => {
                    const option = document.createElement('option');
                    option.value = branch.id;
                    option.textContent = branch.name;
                    branchSelect.appendChild(option);
                });
                
                branchSelect.disabled = false;
                departmentSelect.disabled = true;
                areaSelect.disabled = true;
            } catch (error) {
                console.error('Failed to fetch branches:', error);
            }
        });

        branchSelect.addEventListener('change', async function() {
            departmentSelect.innerHTML = '<option value="">Select Department</option>';
            areaSelect.innerHTML = '<option value="">Select Area</option>';
            
            if (!this.value) {
                departmentSelect.disabled = true;
                areaSelect.disabled = true;
                return;
            }

            try {
                const response = await fetch(`index.php?action=get_departments&branch_id=${this.value}`);
                const departments = await response.json();
                
                departments.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.id;
                    option.textContent = dept.name;
                    departmentSelect.appendChild(option);
                });
                
                departmentSelect.disabled = false;
                areaSelect.disabled = true;
            } catch (error) {
                console.error('Failed to fetch departments:', error);
            }
        });

        departmentSelect.addEventListener('change', async function() {
            areaSelect.innerHTML = '<option value="">Select Area</option>';
            
            if (!this.value) {
                areaSelect.disabled = true;
                return;
            }

            try {
                const response = await fetch(`index.php?action=get_areas&department_id=${this.value}`);
                const areas = await response.json();
                
                areas.forEach(area => {
                    const option = document.createElement('option');
                    option.value = area.id;
                    option.textContent = area.name;
                    areaSelect.appendChild(option);
                });
                
                areaSelect.disabled = false;
            } catch (error) {
                console.error('Failed to fetch areas:', error);
            }
        });

        // Set location selects based on equipment data
        async function setLocationSelects(data) {
            // Set country and trigger change
            countrySelect.value = data.country_id;
            await new Promise(resolve => {
                countrySelect.dispatchEvent(new Event('change'));
                setTimeout(resolve, 100);
            });

            // Wait for branches to load and set branch
            await new Promise(resolve => {
                const checkBranches = setInterval(() => {
                    if (branchSelect.querySelector(`option[value="${data.branch_id}"]`)) {
                        clearInterval(checkBranches);
                        branchSelect.value = data.branch_id;
                        branchSelect.dispatchEvent(new Event('change'));
                        resolve();
                    }
                }, 50);
            });

            // Wait for departments to load and set department
            await new Promise(resolve => {
                const checkDepts = setInterval(() => {
                    if (departmentSelect.querySelector(`option[value="${data.department_id}"]`)) {
                        clearInterval(checkDepts);
                        departmentSelect.value = data.department_id;
                        departmentSelect.dispatchEvent(new Event('change'));
                        resolve();
                    }
                }, 50);
            });

            // Wait for areas to load and set area
            await new Promise(resolve => {
                const checkAreas = setInterval(() => {
                    if (areaSelect.querySelector(`option[value="${data.area_id}"]`)) {
                        clearInterval(checkAreas);
                        areaSelect.value = data.area_id;
                        resolve();
                    }
                }, 50);
            });
        }

        document.getElementById('serial').addEventListener('change', async function() {
            const serial = this.value.trim();
            if (!serial) return;

            try {
                const response = await fetch(`index.php?action=api_get_equipment&serial=${serial}`);
                const data = await response.json();

                if (data.error) {
                    showError('Equipment not found');
                    document.getElementById('auditForm').classList.add('d-none');
                    return;
                }

                // Fill current info
                document.getElementById('currentModel').textContent = `${data.type_name} - ${data.model_name}`;
                document.getElementById('currentStatus').textContent = data.status;
                document.getElementById('currentAssigned').textContent = data.user_name || '-';
                document.getElementById('currentLocation').textContent = data.location || '-';

                // Set form values
                document.getElementById('equipmentId').value = data.id;
                document.getElementById('serialNumber').value = serial;
                document.getElementById('currentStatus').value = data.status;
                document.getElementById('currentLocationId').value = data.area_id;
                document.getElementById('currentAssignedToId').value = data.assigned_to_id;
                document.querySelector('[name="new_status"]').value = data.status;
                document.querySelector('[name="new_assigned_to_id"]').value = data.assigned_to_id || '';

                // Set location selects
                await setLocationSelects(data);

                // Show form
                document.getElementById('auditForm').classList.remove('d-none');
                document.getElementById('errorMessage').classList.add('d-none');

            } catch (error) {
                showError('Failed to fetch equipment information');
            }
        });

        document.getElementById('updateForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('index.php?action=api_submit_audit', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.error) {
                    showError(result.error);
                    return;
                }
                
                // Clear form and show success
                document.getElementById('serial').value = '';
                this.reset();
                document.getElementById('auditForm').classList.add('d-none');
                alert('Audit submitted successfully');
                
            } catch (error) {
                showError('Failed to submit audit');
            }
        });

        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.classList.remove('d-none');
        }
    </script>
</body>
</html> 