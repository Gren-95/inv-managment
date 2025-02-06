<!DOCTYPE html>
<html>
<head>
    <title>IT Equipment Management - Write Off Equipment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Write Off Equipment</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Equipment Details</h5>
                <p>
                    <strong>Type-Model:</strong> <?= htmlspecialchars($item['type_name']) ?> - <?= htmlspecialchars($item['model_name']) ?><br>
                    <strong>Current Status:</strong> <?= htmlspecialchars($item['status']) ?>
                </p>
            </div>
        </div>

        <form method="POST" action="?action=write_off" class="mt-3">
            <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
            
            <div class="mb-3">
                <label for="write_off_type" class="form-label">Write-off Type</label>
                <select name="write_off_type" id="write_off_type" class="form-control" required>
                    <option value="">Select Type</option>
                    <option value="charity">Charity Donation</option>
                    <option value="broken">Broken/Damaged</option>
                    <option value="lost">Lost/Stolen</option>
                    <option value="sold">Sold</option>
                    <option value="recycled">Recycled</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="write_off_comment" class="form-label">Comment</label>
                <textarea name="write_off_comment" id="write_off_comment" class="form-control" 
                          rows="3" required placeholder="Please provide details about the write-off reason"></textarea>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-danger" 
                        onclick="return confirm('Are you sure you want to write off this equipment? This action cannot be undone.')">
                    Confirm Write-Off
                </button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html> 