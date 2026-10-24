<?php
require_once __DIR__ . '/../layouts/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">My Service Bookings</h2>
        <p class="text-muted mb-0">Track all ongoing and past service requests, invoices, and leave reviews</p>
    </div>
    <a href="/customer/search" class="btn btn-primary-custom">
        <i class="bi bi-plus-circle me-1"></i> Book New Service
    </a>
</div>

<?php if (empty($bookings)): ?>
    <div class="card card-modern p-5 text-center my-4">
        <div class="p-3 bg-light rounded-circle d-inline-block mx-auto mb-3 fs-1 text-muted">
            <i class="bi bi-calendar-x"></i>
        </div>
        <h4 class="fw-bold">No bookings yet</h4>
        <p class="text-muted">You haven't scheduled any services yet. Explore services to make your first booking!</p>
        <div>
            <a href="/customer/search" class="btn btn-primary-custom rounded-pill px-4">Browse Services</a>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($bookings as $b): ?>
            <?php 
            $status = $b['status'] ?? 'Pending';
            $badgeClass = match($status) {
                'Pending' => 'bg-warning text-dark',
                'Confirmed' => 'bg-info text-dark',
                'Completed' => 'bg-success',
                'Cancelled' => 'bg-danger',
                default => 'bg-secondary'
            };
            $bId = (string)$b['_id'];
            $pId = (string)($b['provider_id'] ?? '');
            ?>
            <div class="col-12">
                <div class="card card-modern p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-md-3">
                            <span class="badge <?= $badgeClass ?> px-3 py-1 mb-2 fs-7"><?= htmlspecialchars($status) ?></span>
                            <h5 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($b['service']['name'] ?? 'Home Service') ?></h5>
                            <small class="text-muted d-block"><i class="bi bi-receipt me-1"></i> <?= htmlspecialchars($b['invoice']['invoice_number'] ?? 'INV-PENDING') ?></small>
                            <small class="text-muted d-block"><i class="bi bi-calendar me-1"></i> <?= htmlspecialchars($b['date'] ?? '') ?> (<?= htmlspecialchars($b['time_slot'] ?? '') ?>)</small>
                        </div>

                        <div class="col-md-3 border-start-md">
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.75rem;">Service Provider</small>
                            <h6 class="fw-bold mb-1 text-primary mt-1"><?= htmlspecialchars($b['provider']['business_name'] ?? 'Local Specialist') ?></h6>
                            <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($b['provider']['city'] ?? 'New York') ?></div>
                            <div class="small text-muted"><i class="bi bi-star-fill text-warning me-1"></i> Rating: <?= htmlspecialchars($b['provider']['rating_avg'] ?? '4.5') ?> / 5.0</div>
                        </div>

                        <div class="col-md-2">
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.75rem;">Payment</small>
                            <div class="fs-5 fw-bold text-dark mt-1">$<?= number_format((float)($b['payment']['amount'] ?? 50.00), 2) ?></div>
                            <span class="badge bg-light text-dark border text-uppercase" style="font-size:0.7rem;">
                                <?= htmlspecialchars($b['payment']['method'] ?? 'cash') ?> &bull; <?= htmlspecialchars($b['payment']['status'] ?? 'pending') ?>
                            </span>
                        </div>

                        <div class="col-md-4 text-md-end">
                            <div class="d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
                                <?php if (!empty($b['review'])): ?>
                                    <div class="text-start bg-light p-2 rounded-3 border w-100 mb-2">
                                        <div class="small fw-bold text-success">
                                            <i class="bi bi-check2-circle me-1"></i> Your Review: 
                                            <?php for($i = 0; $i < ($b['review']['rating'] ?? 5); $i++): ?>
                                                <i class="bi bi-star-fill text-warning"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <?php if(!empty($b['review']['comment'])): ?>
                                            <div class="small text-muted mt-1">"<?= htmlspecialchars($b['review']['comment']) ?>"</div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#reviewModal<?= $bId ?>">
                                        <i class="bi bi-star me-1"></i> Leave Review
                                    </button>
                                <?php endif; ?>

                                <?php if (!empty($b['report'])): ?>
                                    <span class="badge bg-danger-subtle text-danger p-2 rounded-pill small">
                                        <i class="bi bi-flag-fill me-1"></i> Complaint: <?= htmlspecialchars($b['report']['status'] ?? 'Open') ?>
                                    </span>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#reportModal<?= $bId ?>">
                                        <i class="bi bi-exclamation-octagon me-1"></i> Report Issue
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Modal -->
            <div class="modal fade" id="reviewModal<?= $bId ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header bg-light rounded-top-4">
                            <h5 class="modal-title fw-bold">Rate & Review Service</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="/review/store">
                            <div class="modal-body p-4">
                                <input type="hidden" name="booking_id" value="<?= $bId ?>">
                                <input type="hidden" name="provider_id" value="<?= $pId ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Rating (1 to 5 Stars)</label>
                                    <select name="rating" class="form-select" required>
                                        <option value="5" selected>5 Stars - Excellent Service</option>
                                        <option value="4">4 Stars - Very Good</option>
                                        <option value="3">3 Stars - Average</option>
                                        <option value="2">2 Stars - Poor</option>
                                        <option value="1">1 Star - Very Unsatisfied</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Your Feedback / Comments</label>
                                    <textarea name="comment" class="form-control" rows="3" placeholder="Describe the quality of service, punctuality, and experience..." required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer bg-light rounded-bottom-4">
                                <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Submit Review</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Report / Complaint Modal -->
            <div class="modal fade" id="reportModal<?= $bId ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header bg-danger text-white rounded-top-4">
                            <h5 class="modal-title fw-bold"><i class="bi bi-shield-exclamation me-1"></i> Raise Complaint</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="/report/store">
                            <div class="modal-body p-4">
                                <input type="hidden" name="booking_id" value="<?= $bId ?>">
                                <input type="hidden" name="provider_id" value="<?= $pId ?>">
                                
                                <p class="text-muted small mb-3">Our admin support team will investigate your report and contact the provider directly.</p>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Reason / Details of Complaint</label>
                                    <textarea name="reason" class="form-control" rows="3" placeholder="Please describe the issue (e.g. provider delayed, work incomplete, billing dispute)..." required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer bg-light rounded-bottom-4">
                                <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger rounded-pill px-4">Submit Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
