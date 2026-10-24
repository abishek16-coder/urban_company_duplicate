<?php
require_once __DIR__ . '/../layouts/header.php';
$provId = (string)($provider['_id'] ?? '');
$provCategories = array_map(function($id) { return (string)$id; }, (array)($provider['category_ids'] ?? []));
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h2 class="fw-bold mb-0"><?= htmlspecialchars($provider['business_name'] ?? 'Provider Services') ?></h2>
            <?php if($provider['is_verified'] ?? false): ?>
                <span class="badge bg-primary-subtle text-primary"><i class="bi bi-patch-check-fill"></i> Verified Specialist</span>
            <?php endif; ?>
        </div>
        <p class="text-muted mb-0">
            <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($provider['city'] ?? 'City') ?> &bull; 
            <i class="bi bi-star-fill text-warning me-1"></i> <?= htmlspecialchars($provider['rating_avg'] ?? '5.0') ?> (<?= htmlspecialchars($provider['rating_count'] ?? '0') ?> reviews)
        </p>
    </div>
    <div>
        <a href="/provider/bookings" class="btn btn-primary-custom">
            <i class="bi bi-calendar-check me-1"></i> View Customer Bookings
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Business Profile Management -->
    <div class="col-lg-5">
        <div class="card card-modern p-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-gear text-primary me-2"></i>Update Business Profile</h5>
            <form method="POST" action="/provider/profile/update">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Business / Trading Name</label>
                    <input type="text" name="business_name" class="form-control" value="<?= htmlspecialchars($provider['business_name'] ?? '') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Primary Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Select a category</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= (string)$cat['_id'] ?>" <?= in_array((string)$cat['_id'], $provCategories) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">City / Service Area</label>
                    <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($provider['city'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Business Description & Expertise</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Tell customers about your qualifications, warranty, and experience..."><?= htmlspecialchars($provider['description'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-check-lg me-1"></i> Save Profile Details
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: Service Offerings -->
    <div class="col-lg-7">
        <div class="card card-modern p-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-plus-circle text-success me-2"></i>Add New Service Offering</h5>
            <form method="POST" action="/provider/services/store" class="row g-3">
                <div class="col-md-7">
                    <label class="form-label fw-semibold small">Service Title</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Ceiling Fan Installation" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold small">Price ($ USD)</label>
                    <input type="number" step="0.01" name="price" class="form-control" placeholder="45.00" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold small">Service Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Explain what is included in this service..."></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Estimated Duration</label>
                    <input type="text" name="duration" class="form-control" placeholder="e.g. 1.5 hours" required>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-plus-lg me-1"></i> Publish Service
                    </button>
                </div>
            </form>
        </div>

        <div class="card card-modern p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-list-check text-primary me-2"></i>Active Services (<?= count($services) ?>)</h5>
            </div>
            <?php if(empty($services)): ?>
                <p class="text-muted text-center py-4 mb-0">No services added yet. Fill out the form above to list your first service!</p>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach($services as $srv): ?>
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($srv['name']) ?></h6>
                                <p class="text-muted small mb-1"><?= htmlspecialchars($srv['description'] ?? '') ?></p>
                                <span class="badge bg-light text-dark border"><i class="bi bi-clock me-1"></i> <?= htmlspecialchars($srv['duration'] ?? '1 hr') ?></span>
                            </div>
                            <div class="text-end">
                                <span class="fs-5 fw-bold text-success">$<?= number_format((float)($srv['price'] ?? 0), 2) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
