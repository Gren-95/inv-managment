<!DOCTYPE html>
<html>
<head>
    <title>Locations - ITEM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .actions-column {
            text-align: right;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Locations</h1>
            <a href="index.php" class="btn btn-secondary">Back</a>
        </div>

        <div class="row">
            <!-- Countries -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Countries</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="mb-3">
                            <input type="hidden" name="type" value="country">
                            <div class="input-group">
                                <input type="text" name="name" class="form-control" placeholder="Country Name" required>
                                <button type="submit" class="btn btn-primary">Add Country</button>
                            </div>
                        </form>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="actions-column">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($countries as $country): ?>
                                <tr>
                                    <td><?= htmlspecialchars($country['name']) ?></td>
                                    <td class="actions-column">
                                        <button class="btn btn-sm btn-warning" onclick="editCountry(<?= $country['id'] ?>, '<?= htmlspecialchars($country['name']) ?>')">Edit</button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteLocation('country', <?= $country['id'] ?>)">Delete</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Branches -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Branches</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="mb-3">
                            <input type="hidden" name="type" value="branch">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <select name="country_id" class="form-control" required>
                                        <option value="">Select Country</option>
                                        <?php foreach ($countries as $country): ?>
                                            <option value="<?= $country['id'] ?>"><?= htmlspecialchars($country['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="name" class="form-control" placeholder="Branch Name" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Add</button>
                                </div>
                            </div>
                        </form>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Country</th>
                                    <th class="actions-column">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($branches as $branch): ?>
                                <tr>
                                    <td><?= htmlspecialchars($branch['name']) ?></td>
                                    <td><?= htmlspecialchars($branch['country_name']) ?></td>
                                    <td class="actions-column">
                                        <button class="btn btn-sm btn-warning" onclick="editBranch(<?= $branch['id'] ?>, '<?= htmlspecialchars($branch['name']) ?>', <?= $branch['country_id'] ?>)">Edit</button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteLocation('branch', <?= $branch['id'] ?>)">Delete</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Departments -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Departments</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="mb-3">
                            <input type="hidden" name="type" value="department">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <select name="branch_id" class="form-control" required>
                                        <option value="">Select Branch</option>
                                        <?php foreach ($branches as $branch): ?>
                                            <option value="<?= $branch['id'] ?>">
                                                <?= htmlspecialchars($branch['country_name'] . ' - ' . $branch['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="name" class="form-control" placeholder="Department Name" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Add</button>
                                </div>
                            </div>
                        </form>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Branch</th>
                                    <th class="actions-column">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($departments as $dept): ?>
                                <tr>
                                    <td><?= htmlspecialchars($dept['name']) ?></td>
                                    <td><?= htmlspecialchars($dept['branch_name']) ?></td>
                                    <td class="actions-column">
                                        <button class="btn btn-sm btn-warning" onclick="editDepartment(<?= $dept['id'] ?>, '<?= htmlspecialchars($dept['name']) ?>', <?= $dept['branch_id'] ?>)">Edit</button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteLocation('department', <?= $dept['id'] ?>)">Delete</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Areas -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Areas</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="mb-3">
                            <input type="hidden" name="type" value="area">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <select name="department_id" class="form-control" required>
                                        <option value="">Select Department</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= $dept['id'] ?>">
                                                <?= htmlspecialchars($dept['country_name'] . ' - ' . $dept['branch_name'] . ' - ' . $dept['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="name" class="form-control" placeholder="Area Name" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Add</button>
                                </div>
                            </div>
                        </form>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th class="actions-column">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($areas as $area): ?>
                                <tr>
                                    <td><?= htmlspecialchars($area['name']) ?></td>
                                    <td><?= htmlspecialchars($area['department_name']) ?></td>
                                    <td class="actions-column">
                                        <button class="btn btn-sm btn-warning" onclick="editArea(<?= $area['id'] ?>, '<?= htmlspecialchars($area['name']) ?>', <?= $area['department_id'] ?>)">Edit</button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteLocation('area', <?= $area['id'] ?>)">Delete</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="editId" name="id">
                        <input type="hidden" id="editType" name="type">
                        
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        
                        <div class="mb-3" id="editParentDiv">
                            <label class="form-label" id="editParentLabel">Parent</label>
                            <select class="form-control" id="editParentId" name="parent_id" required>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveEdit()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        
        function editCountry(id, name) {
            document.getElementById('editId').value = id;
            document.getElementById('editType').value = 'country';
            document.getElementById('editName').value = name;
            document.getElementById('editParentDiv').style.display = 'none';
            document.getElementById('editParentId').removeAttribute('required');
            editModal.show();
        }
        
        function editBranch(id, name, countryId) {
            document.getElementById('editId').value = id;
            document.getElementById('editType').value = 'branch';
            document.getElementById('editName').value = name;
            document.getElementById('editParentLabel').textContent = 'Country';
            document.getElementById('editParentId').innerHTML = document.querySelector('select[name="country_id"]').innerHTML;
            document.getElementById('editParentId').value = countryId;
            document.getElementById('editParentDiv').style.display = 'block';
            document.getElementById('editParentId').setAttribute('required', 'required');
            editModal.show();
        }
        
        function editDepartment(id, name, branchId) {
            document.getElementById('editId').value = id;
            document.getElementById('editType').value = 'department';
            document.getElementById('editName').value = name;
            document.getElementById('editParentLabel').textContent = 'Branch';
            document.getElementById('editParentId').innerHTML = document.querySelector('select[name="branch_id"]').innerHTML;
            document.getElementById('editParentId').value = branchId;
            document.getElementById('editParentDiv').style.display = 'block';
            document.getElementById('editParentId').setAttribute('required', 'required');
            editModal.show();
        }
        
        function editArea(id, name, departmentId) {
            document.getElementById('editId').value = id;
            document.getElementById('editType').value = 'area';
            document.getElementById('editName').value = name;
            document.getElementById('editParentLabel').textContent = 'Department';
            document.getElementById('editParentId').innerHTML = document.querySelector('select[name="department_id"]').innerHTML;
            document.getElementById('editParentId').value = departmentId;
            document.getElementById('editParentDiv').style.display = 'block';
            document.getElementById('editParentId').setAttribute('required', 'required');
            editModal.show();
        }
        
        async function saveEdit() {
            const form = document.getElementById('editForm');
            const formData = new FormData(form);
            formData.append('action', 'edit_location');
            
            try {
                const response = await fetch('index.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    editModal.hide();
                    location.reload();
                } else {
                    alert(result.error || 'Failed to update location');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to update location');
            }
        }
        
        async function deleteLocation(type, id) {
            if (!confirm('Are you sure you want to delete this location?')) {
                return;
            }
            
            try {
                const response = await fetch('index.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete_location',
                        type: type,
                        id: id
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.error || 'Failed to delete location');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to delete location');
            }
        }
    </script>
</body>
</html> 