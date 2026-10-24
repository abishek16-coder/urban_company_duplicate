<?php
require_once __DIR__ . '/../layouts/header.php';
use App\Helpers\CSRF;
use App\Helpers\Session;
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card card-modern p-4">
            <div class="card-body p-0">
                <div class="text-center mb-4">
                    <div class="p-3 bg-primary-subtle text-primary rounded-circle d-inline-block mb-2 fs-3">
                        <i class="bi bi-box-arrow-in-right"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Welcome Back</h3>
                    <p class="text-muted small">Sign in to manage bookings or services</p>
                </div>

                <form method="POST" action="/auth/login" id="loginForm">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                            <input type="email" id="email" name="email" class="form-control" placeholder="name@example.com" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100 py-2 mb-3">
                        <i class="bi bi-arrow-right-circle me-1"></i> Sign In
                    </button>
                </form>

                <div class="border-top pt-3 text-center">
                    <p class="small text-muted mb-2">Don't have an account? <a href="/auth/register" class="fw-semibold text-primary">Register here</a></p>
                    
                    <div class="bg-light p-3 rounded-3 mt-3 text-start border">
                        <div class="small fw-bold text-muted mb-2"><i class="bi bi-lightning-charge-fill text-warning"></i> Fast 1-Click Demo Fill:</div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-xs btn-outline-danger btn-sm" onclick="fillCreds('admin@serviceconnect.com', 'Admin@123')">
                                <i class="bi bi-shield-lock"></i> Admin
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-warning btn-sm" onclick="fillCreds('provider@serviceconnect.com', 'Provider@123')">
                                <i class="bi bi-tools"></i> Provider
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-success btn-sm" onclick="fillCreds('customer@serviceconnect.com', 'Customer@123')">
                                <i class="bi bi-person"></i> Customer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fillCreds(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
