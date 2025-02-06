<!DOCTYPE html>
<html>
<head>
    <title>Setup Password - ITEM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Setup Password</h3>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <?php if (!isset($ppid)): ?>
                            <!-- Step 1: Enter PPID -->
                            <form method="POST" action="?action=setup_password">
                                <div class="mb-3">
                                    <label class="form-label">Enter PPID to Reset Password</label>
                                    <input type="text" name="check_ppid" class="form-control" required autofocus>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Continue</button>
                            </form>
                        <?php else: ?>
                            <!-- Step 2: Set new password -->
                            <p class="text-muted mb-4">Setting password for PPID: <?= htmlspecialchars($ppid) ?></p>
                            <form method="POST" action="?action=setup_password">
                                <input type="hidden" name="ppid" value="<?= htmlspecialchars($ppid) ?>">
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="password" class="form-control" required 
                                           minlength="8" autofocus>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control" 
                                           required minlength="8">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Set Password</button>
                            </form>
                        <?php endif; ?>
                        
                        <div class="mt-3 text-center">
                            <a href="index.php" class="text-muted">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 