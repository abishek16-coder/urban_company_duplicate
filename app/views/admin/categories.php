<?php
require_once __DIR__ . '/../layouts/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">Manage Categories</h2>
        <p class="text-muted mb-0">Add, organize, and manage home service categories</p>
    </div>
    <a href="/admin/dashboard" class="btn btn-outline-dark">
        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
    </a>
</div>

<div class="row g-4">
    <!-- Add Category Form -->
    <div class="col-lg-5">
        <div class="card card-modern p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-folder-plus text-primary me-2"></i>Add Category</h5>
            <form method="POST" action="/admin/categories">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Category Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Appliance Repair, AC Servicing" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Bootstrap Icon Class</label>
                    <input type="text" name="icon" id="iconInput" class="form-control" value="bi-wrench" placeholder="bi-wrench, bi-lightning, bi-brush..." required>
                    <div class="form-text small">
                        Quick pick: 
                        <a href="javascript:void(0)" onclick="document.getElementById('iconInput').value='bi-wrench'" class="me-1 badge bg-light text-dark border">wrench</a>
                        <a href="javascript:void(0)" onclick="document.getElementById('iconInput').value='bi-lightning-charge'" class="me-1 badge bg-light text-dark border">lightning</a>
                        <a href="javascript:void(0)" onclick="document.getElementById('iconInput').value='bi-droplet'" class="me-1 badge bg-light text-dark border">droplet</a>
                        <a href="javascript:void(0)" onclick="document.getElementById('iconInput').value='bi-brush'" class="me-1 badge bg-light text-dark border">brush</a>
                        <a href="javascript:void(0)" onclick="document.getElementById('iconInput').value='bi-snow'" class="me-1 badge bg-light text-dark border">snow (AC)</a>
                        <a href="javascript:void(0)" onclick="document.getElementById('iconInput').value='bi-house-gear'" class="me-1 badge bg-light text-dark border">house</a>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary-custom w-100">
                    <i class="bi bi-plus-lg me-1"></i> Create Category
                </button>
            </form>
        </div>
    </div>

    <!-- Existing Categories List -->
    <div class="col-lg-7">
        <div class="card card-modern p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-grid-fill text-primary me-2"></i>Existing Categories (<?= count($categories) ?>)</h5>
            <div class="list-group list-group-flush">
                <?php foreach($categories as $cat): ?>
                    <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2 bg-primary-subtle text-primary rounded-circle fs-4 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i class="bi <?= htmlspecialchars($cat['icon'] ?? 'bi-folder') ?>"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($cat['name']) ?></h6>
                                <small class="text-muted">Slug: <code><?= htmlspecialchars($cat['slug']) ?></code> &bull; Icon: <?= htmlspecialchars($cat['icon'] ?? 'default') ?></small>
                            </div>
                        </div>
                        <div>
                            <a href="/admin/categories/delete?id=<?= (string)$cat['_id'] ?>" 
                               class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                               onclick="return confirm('Delete category <?= htmlspecialchars($cat['name']) ?>?');">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
