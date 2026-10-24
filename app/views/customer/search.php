<?php
require_once __DIR__ . '/../layouts/header.php';
$searchQuery = $_GET['q'] ?? '';
$activeCat = $_GET['category'] ?? '';
?>
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h2 class="fw-bold mb-1">Available Services</h2>
            <p class="text-muted mb-0">Browse and book verified professionals for your home</p>
        </div>
        <form method="GET" action="/customer/search" class="d-flex gap-2">
            <input type="text" name="q" value="<?= htmlspecialchars($searchQuery) ?>" class="form-control rounded-pill px-3" placeholder="Search service name...">
            <button class="btn btn-primary-custom rounded-pill px-3" type="submit"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <!-- Category filter pills -->
    <div class="d-flex gap-2 flex-wrap mb-4 align-items-center">
        <span class="small text-muted fw-semibold me-1">Filter:</span>
        <a href="/customer/search" class="category-pill <?= empty($activeCat) ? 'border-primary bg-primary text-white' : '' ?>">
            All Services
        </a>
        <?php if (!empty($categories)): foreach($categories as $c): ?>
            <a href="/customer/search?category=<?= urlencode($c['slug']) ?>" class="category-pill <?= ($activeCat === $c['slug']) ? 'border-primary bg-primary text-white' : '' ?>">
                <i class="bi <?= htmlspecialchars($c['icon'] ?? 'bi-folder') ?>"></i> <?= htmlspecialchars($c['name']) ?>
            </a>
        <?php endforeach; endif; ?>
    </div>
</div>

<?php if(empty($services)): ?>
    <div class="card card-modern p-5 text-center my-4">
        <div class="p-3 bg-light rounded-circle d-inline-block mx-auto mb-3 fs-1 text-muted">
            <i class="bi bi-search"></i>
        </div>
        <h4 class="fw-bold">No services found</h4>
        <p class="text-muted">Try clearing your search query or choosing another category.</p>
        <div>
            <a href="/customer/search" class="btn btn-outline-primary rounded-pill">View All Services</a>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach($services as $srv): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card card-modern h-100 p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($srv['name']) ?></h5>
                            <span class="badge bg-success-subtle text-success fs-6 fw-bold">
                                $<?= number_format((float)($srv['price'] ?? 0), 2) ?>
                            </span>
                        </div>
                        <p class="text-muted small mb-3"><?= htmlspecialchars($srv['description'] ?? 'Quality professional service.') ?></p>
                        
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="fw-semibold text-dark small">
                                    <i class="bi bi-person-badge text-primary me-1"></i>
                                    <?= htmlspecialchars($srv['provider']['business_name'] ?? 'Verified Specialist') ?>
                                </span>
                                <span class="badge bg-white text-dark border">
                                    <i class="bi bi-star-fill text-warning"></i> <?= htmlspecialchars($srv['provider']['rating_avg'] ?? '5.0') ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted">
                                <span><i class="bi bi-clock me-1"></i> <?= htmlspecialchars($srv['duration'] ?? '1 hr') ?></span>
                                <span><i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($srv['provider']['city'] ?? 'Local') ?></span>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary-custom w-100" data-bs-toggle="modal" data-bs-target="#bookModal<?= $srv['_id'] ?>">
                        <i class="bi bi-calendar-plus me-1"></i> Book Service
                    </button>
                </div>
            </div>

            <!-- Booking Modal -->
            <div class="modal fade" id="bookModal<?= $srv['_id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header bg-light rounded-top-4">
                            <h5 class="modal-title fw-bold">Book: <?= htmlspecialchars($srv['name']) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="/booking/store">
                            <div class="modal-body p-4">
                                <input type="hidden" name="service_id" value="<?= $srv['_id'] ?>">
                                <input type="hidden" name="provider_id" value="<?= $srv['provider_id'] ?>">
                                
                                <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($srv['provider']['business_name'] ?? 'Provider') ?></div>
                                        <small class="text-muted">Estimated Duration: <?= htmlspecialchars($srv['duration'] ?? '1 hr') ?></small>
                                    </div>
                                    <div class="fs-5 fw-bold text-primary">$<?= number_format((float)($srv['price'] ?? 0), 2) ?></div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Choose Date</label>
                                    <input type="date" name="date" class="form-control" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Select Preferred Time Slot</label>
                                    <select name="time_slot" class="form-select" required>
                                        <option value="09:00 AM - 11:00 AM">Morning (09:00 AM - 11:00 AM)</option>
                                        <option value="11:00 AM - 01:00 PM">Noon (11:00 AM - 01:00 PM)</option>
                                        <option value="02:00 PM - 04:00 PM" selected>Afternoon (02:00 PM - 04:00 PM)</option>
                                        <option value="04:00 PM - 06:00 PM">Evening (04:00 PM - 06:00 PM)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Payment Method</label>
                                    <select name="payment_method" class="form-select">
                                        <option value="cash">Pay after Service (Cash/Card)</option>
                                        <option value="online">Online Payment (Simulated)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer bg-light rounded-bottom-4">
                                <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Confirm Booking</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
