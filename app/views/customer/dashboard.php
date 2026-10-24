<?php
require_once __DIR__ . '/../layouts/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">Customer Dashboard</h2>
        <p class="text-muted mb-0">Welcome back, <?= htmlspecialchars(App\Helpers\Session::get('name') ?? 'Customer') ?>! What service can we help you with today?</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/customer/search" class="btn btn-primary-custom"><i class="bi bi-search me-1"></i> Browse Services</a>
        <a href="/customer/bookings" class="btn btn-outline-dark"><i class="bi bi-calendar-check me-1"></i> My Bookings</a>
    </div>
</div>

<!-- Search Bar -->
<div class="card card-modern p-4 mb-4 bg-white">
    <h5 class="fw-bold mb-3"><i class="bi bi-search text-primary me-2"></i>Find Local Household Services</h5>
    <form method="GET" action="/customer/search" class="row g-2">
        <div class="col-md-9">
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Search by service name or keyword (e.g. Wiring, Short circuit, Pipe leak)...">
            </div>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary-custom w-100" type="submit">Search Services</button>
        </div>
    </form>
</div>

<!-- Categories -->
<div class="mb-5">
    <h4 class="fw-bold mb-3">Service Categories</h4>
    <div class="row g-3">
        <?php foreach($categories as $cat): ?>
            <div class="col-md-3 col-6">
                <a href="/customer/search?category=<?= urlencode($cat['slug']) ?>" class="card card-modern p-4 text-center text-decoration-none text-dark h-100">
                    <div class="p-3 bg-primary-subtle text-primary rounded-circle d-inline-block mx-auto mb-3 fs-2">
                        <i class="bi <?= htmlspecialchars($cat['icon'] ?? 'bi-folder') ?>"></i>
                    </div>
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($cat['name']) ?></h5>
                    <small class="text-muted">Click to browse & book</small>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Recent Bookings Snapshot -->
<?php if(!empty($recentBookings)): ?>
<div class="card card-modern p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Recent Bookings</h5>
        <a href="/customer/bookings" class="small fw-semibold text-primary">View All Bookings <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Service</th>
                    <th>Provider</th>
                    <th>Scheduled Date</th>
                    <th>Time Slot</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recentBookings as $b): ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($b['service']['name'] ?? 'Service') ?></td>
                        <td><?= htmlspecialchars($b['provider']['business_name'] ?? 'Provider') ?></td>
                        <td><?= htmlspecialchars($b['date'] ?? '') ?></td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($b['time_slot'] ?? '') ?></span></td>
                        <td>
                            <?php 
                            $badgeClass = match($b['status'] ?? 'Pending') {
                                'Pending' => 'bg-warning text-dark',
                                'Confirmed' => 'bg-info text-dark',
                                'Completed' => 'bg-success',
                                'Cancelled' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($b['status'] ?? 'Pending') ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
