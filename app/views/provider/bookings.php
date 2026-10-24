<?php
require_once __DIR__ . '/../layouts/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">Customer Service Bookings</h2>
        <p class="text-muted mb-0">Review incoming requests, manage job statuses, and view customer feedback</p>
    </div>
    <a href="/provider/dashboard" class="btn btn-outline-dark">
        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
    </a>
</div>

<?php if (empty($bookings)): ?>
    <div class="card card-modern p-5 text-center my-4">
        <div class="p-3 bg-light rounded-circle d-inline-block mx-auto mb-3 fs-1 text-muted">
            <i class="bi bi-calendar-check"></i>
        </div>
        <h4 class="fw-bold">No booking requests yet</h4>
        <p class="text-muted">When customers book your services, their appointments will appear here.</p>
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
            ?>
            <div class="col-12">
                <div class="card card-modern p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-md-3">
                            <span class="badge <?= $badgeClass ?> px-3 py-1 mb-2 fs-7"><?= htmlspecialchars($status) ?></span>
                            <h5 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($b['service']['name'] ?? 'Home Service') ?></h5>
                            <small class="text-muted d-block"><i class="bi bi-receipt me-1"></i> <?= htmlspecialchars($b['invoice']['invoice_number'] ?? 'INV-PENDING') ?></small>
                            <small class="text-muted d-block"><i class="bi bi-calendar-event me-1"></i> <?= htmlspecialchars($b['date'] ?? '') ?> (<?= htmlspecialchars($b['time_slot'] ?? '') ?>)</small>
                        </div>

                        <div class="col-md-3 border-start-md">
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.75rem;">Customer Details</small>
                            <h6 class="fw-bold mb-1 mt-1 text-dark"><?= htmlspecialchars($b['customer']['name'] ?? 'Customer') ?></h6>
                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($b['customer']['phone'] ?? 'N/A') ?></div>
                            <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($b['customer']['address'] ?? '') ?>, <?= htmlspecialchars($b['customer']['city'] ?? '') ?></div>
                        </div>

                        <div class="col-md-2">
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.75rem;">Earnings / Bill</small>
                            <div class="fs-5 fw-bold text-success mt-1">$<?= number_format((float)($b['payment']['amount'] ?? 50.00), 2) ?></div>
                            <span class="badge bg-light text-dark border text-uppercase" style="font-size:0.7rem;">
                                <?= htmlspecialchars($b['payment']['status'] ?? 'pending') ?>
                            </span>
                        </div>

                        <div class="col-md-4 text-md-end">
                            <form method="POST" action="/provider/bookings/update-status" class="d-inline-flex flex-wrap gap-2 justify-content-md-end">
                                <input type="hidden" name="booking_id" value="<?= $bId ?>">
                                
                                <?php if ($status === 'Pending'): ?>
                                    <button type="submit" name="status" value="Confirmed" class="btn btn-sm btn-info text-dark fw-semibold rounded-pill px-3">
                                        <i class="bi bi-check-circle me-1"></i> Accept & Confirm
                                    </button>
                                    <button type="submit" name="status" value="Cancelled" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                        <i class="bi bi-x-circle me-1"></i> Reject
                                    </button>
                                <?php elseif ($status === 'Confirmed'): ?>
                                    <button type="submit" name="status" value="Completed" class="btn btn-sm btn-success fw-semibold rounded-pill px-3">
                                        <i class="bi bi-check2-all me-1"></i> Mark as Completed
                                    </button>
                                    <button type="submit" name="status" value="Cancelled" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                        Cancel
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted small align-self-center">Job <?= htmlspecialchars($status) ?></span>
                                <?php endif; ?>
                            </form>

                            <?php if(!empty($b['review'])): ?>
                                <div class="mt-2 text-start bg-light p-2 rounded-3 border">
                                    <div class="small fw-bold text-success">
                                        <i class="bi bi-star-fill text-warning me-1"></i> Customer Rating: <?= htmlspecialchars($b['review']['rating'] ?? 5) ?> / 5
                                    </div>
                                    <?php if(!empty($b['review']['comment'])): ?>
                                        <div class="small text-muted mt-1">"<?= htmlspecialchars($b['review']['comment']) ?>"</div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if(!empty($b['report'])): ?>
                                <div class="mt-2 text-start bg-danger-subtle p-2 rounded-3 border border-danger">
                                    <div class="small fw-bold text-danger">
                                        <i class="bi bi-flag-fill me-1"></i> Dispute Raised: <?= htmlspecialchars($b['report']['status'] ?? 'Open') ?>
                                    </div>
                                    <div class="small text-danger mt-1">"<?= htmlspecialchars($b['report']['reason'] ?? '') ?>"</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
