<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | <?php echo e(config("app.name")); ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
body{min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#4f46e5,#06b6d4);font-family:"Segoe UI",sans-serif}
.login-card{background:#fff;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.25);max-width:400px;width:100%;padding:2.5rem}
.login-icon{width:64px;height:64px;background:#eef2ff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;color:#4f46e5;margin:0 auto 1rem}
</style>
</head>
<body>
<div class="login-card">
    <div class="login-icon"><i class="bi bi-capsule-pill"></i></div>
    <h4 class="text-center mb-1"><?php echo e(config("app.name")); ?></h4>
    <p class="text-center text-muted mb-4">Admin &amp; Staff Login</p>

    <?php if($errors->any()): ?><div class="alert alert-danger py-2"><?php echo e($errors->first()); ?></div><?php endif; ?>

    <form method="POST" action="<?php echo e(route('login')); ?>">
        <?php echo csrf_field(); ?>
        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" required autofocus></div>
        <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
        <div class="form-check mb-3"><input type="checkbox" name="remember" class="form-check-input" id="rem"><label class="form-check-label" for="rem">Remember me</label></div>
        <button class="btn btn-primary w-100 py-2">Login</button>
    </form>
    <p class="text-center text-muted small mt-4 mb-0">Demo: admin@pharmacy.test / password</p>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/auth/login.blade.php ENDPATH**/ ?>