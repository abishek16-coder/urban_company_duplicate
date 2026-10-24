<?php
require_once __DIR__ . '/../layouts/header.php';
$db = \Config\Database::getDb();
$categories = $db->categories->find()->toArray();
$services = $db->services->find([], ['limit' => 6])->toArray();
?>
<div class="p-5 mb-5 rounded-4 text-white position-relative overflow-hidden shadow" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);">
    <div class="row align-items-center py-3">
        <div class="col-lg-7">
            <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold mb-3 shadow-sm">
                <i class="bi bi-stars text-warning me-1"></i> Trusted Local Home Services
            </span>
            <h1 class="display-4 fw-extrabold mb-3" style="letter-spacing: -0.03em;">Book Top Rated Home Services in Seconds</h1>
            <p class="fs-5 text-light opacity-90 mb-4">
                From electricians and plumbers to full home repair, connect with verified professionals in your city today.
            </p>
            <form method="GET" action="/customer/search" class="d-flex gap-2 bg-white p-2 rounded-pill shadow-lg max-w-lg">
                <input type="text" name="q" class="form-control border-0 shadow-none px-3 py-2 rounded-pill" placeholder="What service do you need? (e.g. Wiring, Plumber)">
                <button type="submit" class="btn btn-primary-custom rounded-pill px-4">
                    <i class="bi bi-search me-1"></i> Search
                </button>
            </form>
        </div>
        <div class="col-lg-5 text-center d-none d-lg-block">
            <div class="p-4 rounded-4 bg-white text-dark shadow-lg mx-auto" style="max-width: 380px;">
                <div class="d-flex align-items-center gap-3 mb-3 text-start">
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-3">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">100% Verified Experts</h6>
                        <small class="text-muted">Background-checked & certified</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 mb-3 text-start">
                    <div class="bg-success-subtle text-success p-3 rounded-circle fs-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Instant Booking & Slots</h6>
                        <small class="text-muted">Choose your preferred date & time</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 text-start">
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle fs-3">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Transparent Pricing</h6>
                        <small class="text-muted">Upfront costs & automated invoices</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category Carousel/Grid -->
<div class="mb-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div>
            <h3 class="fw-bold mb-1">Browse Categories</h3>
            <p class="text-muted mb-0">Select a category to view specialized services</p>
        </div>
        <a href="/customer/search" class="text-primary fw-semibold text-decoration-none">View All <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="row g-3">
        <?php foreach($categories as $cat): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="/customer/search?category=<?= urlencode($cat['slug']) ?>" class="card card-modern p-3 text-decoration-none text-dark h-100 d-flex flex-column align-items-center text-center">
                    <div class="p-3 bg-primary-subtle text-primary rounded-circle mb-3 fs-2">
                        <i class="bi <?= htmlspecialchars($cat['icon'] ?? 'bi-wrench') ?>"></i>
                    </div>
                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($cat['name']) ?></h6>
                    <small class="text-muted">Explore specialists</small>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Test Credentials Box -->
<div class="card card-modern border-primary border-opacity-25 bg-white p-4 mb-5">
    <div class="d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-key-fill text-primary fs-4"></i>
        <h5 class="fw-bold mb-0">Quick Demo Logins (Seed Accounts)</h5>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="p-3 rounded-3 bg-light border">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-danger"><i class="bi bi-shield-lock me-1"></i> Admin</strong>
                    <span class="badge bg-danger">Full Control</span>
                </div>
                <div class="small text-muted">admin@serviceconnect.com</div>
                <div class="small text-muted mb-2">Password: <code>Admin@123</code></div>
                <a href="/auth/login" class="btn btn-sm btn-outline-danger w-100">Login as Admin</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 rounded-3 bg-light border">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-warning"><i class="bi bi-tools me-1"></i> Provider</strong>
                    <span class="badge bg-warning text-dark">Services & Orders</span>
                </div>
                <div class="small text-muted">provider@serviceconnect.com</div>
                <div class="small text-muted mb-2">Password: <code>Provider@123</code></div>
                <a href="/auth/login" class="btn btn-sm btn-outline-warning w-100">Login as Provider</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 rounded-3 bg-light border">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="text-success"><i class="bi bi-person me-1"></i> Customer</strong>
                    <span class="badge bg-success">Book & Review</span>
                </div>
                <div class="small text-muted">customer@serviceconnect.com</div>
                <div class="small text-muted mb-2">Password: <code>Customer@123</code></div>
                <a href="/auth/login" class="btn btn-sm btn-outline-success w-100">Login as Customer</a>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
