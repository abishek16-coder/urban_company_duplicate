<?php
require_once __DIR__ . '/../layouts/header.php';
$db = \Config\Database::getDb();
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">System Administration</h2>
        <p class="text-muted mb-0">Platform overview, live metrics, categories, and dispute resolution</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/categories" class="btn btn-primary-custom">
            <i class="bi bi-grid me-1"></i> Manage Categories
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card card-modern p-4 text-white" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-white-50 small fw-bold text-uppercase">Total Users</span>
                <i class="bi bi-people-fill fs-3 text-white-50"></i>
            </div>
            <h2 class="fw-extrabold mb-0"><?= $counts['users'] ?></h2>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-modern p-4 text-white" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-white-50 small fw-bold text-uppercase">Providers</span>
                <i class="bi bi-briefcase-fill fs-3 text-white-50"></i>
            </div>
            <h2 class="fw-extrabold mb-0"><?= $counts['providers'] ?></h2>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-modern p-4 text-white" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-white-50 small fw-bold text-uppercase">Bookings</span>
                <i class="bi bi-calendar-check-fill fs-3 text-white-50"></i>
            </div>
            <h2 class="fw-extrabold mb-0"><?= $counts['bookings'] ?></h2>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-modern p-4 text-white" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-white-50 small fw-bold text-uppercase">Reports / Issues</span>
                <i class="bi bi-shield-exclamation fs-3 text-white-50"></i>
            </div>
            <h2 class="fw-extrabold mb-0"><?= $counts['reports'] ?></h2>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Bookings -->
    <div class="col-lg-7">
        <div class="card card-modern p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Platform Bookings Activity</h5>
                <span class="badge bg-light text-dark border"><?= count($recentBookings) ?> recent</span>
            </div>
            <?php if(empty($recentBookings)): ?>
                <p class="text-muted text-center py-4 mb-0">No bookings created yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Booking ID</th>
                                <th>Date & Slot</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recentBookings as $b): ?>
                                <tr>
                                    <td>
                                        <code class="small"><?= substr((string)$b['_id'], -8) ?></code>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold"><?= htmlspecialchars($b['date'] ?? '') ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($b['time_slot'] ?? '') ?></div>
                                    </td>
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
            <?php endif; ?>
        </div>
    </div>

    <!-- Customer Disputes & Complaints -->
    <div class="col-lg-5">
        <div class="card card-modern p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-flag-fill text-danger me-2"></i>Customer Complaints</h5>
                <span class="badge bg-danger"><?= count($recentReports) ?> total</span>
            </div>
            <?php if(empty($recentReports)): ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-check-circle-fill text-success fs-2 d-block mb-2"></i>
                    No open complaints at this time. All good!
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach($recentReports as $rep): ?>
                        <div class="list-group-item px-0 py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-danger-subtle text-danger small">Status: <?= htmlspecialchars($rep['status'] ?? 'open') ?></span>
                                <small class="text-muted">Booking: <code><?= substr((string)$rep['booking_id'], -8) ?></code></small>
                            </div>
                            <p class="mb-0 text-dark small fw-semibold">"<?= htmlspecialchars($rep['reason'] ?? 'No reason provided') ?>"</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
