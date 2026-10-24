<?php
use App\Helpers\CSRF;
use App\Helpers\Session;

$userRole = Session::get('role');
$userName = Session::get('name');
$userId = Session::get('user_id');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Connect - Urban Services Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #0ea5e9;
            --dark: #0f172a;
            --surface: #ffffff;
            --background: #f8fafc;
            --border: #e2e8f0;
        }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--background);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1 0 auto;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 0.85rem 0;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.35rem;
            letter-spacing: -0.02em;
            color: #fff !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .navbar-brand i {
            color: #818cf8;
            font-size: 1.5rem;
        }
        .nav-link {
            font-weight: 500;
            color: #cbd5e1 !important;
            padding: 0.5rem 0.9rem !important;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.08);
        }
        .badge-role {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
        }
        .badge-admin { background: #fee2e2; color: #dc2626; }
        .badge-provider { background: #fef3c7; color: #d97706; }
        .badge-customer { background: #dcfce7; color: #16a34a; }
        
        .card-modern {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -2px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-modern:hover {
            transform: translateY(-5px) scale(1.01);
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.1), 0 8px 10px -6px rgba(79, 70, 229, 0.05);
            border-color: #c7d2fe;
        }
        .stat-card {
            border-radius: 16px;
            padding: 1.5rem;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.12);
        }
        .btn-primary-custom {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 0.55rem 1.25rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .btn-primary-custom:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
            color: white;
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
            transform: translateY(-2px);
        }
        .btn-primary-custom:active {
            transform: translateY(0);
        }
        .category-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.1rem;
            border-radius: 10px;
            background: #ffffff;
            border: 1px solid var(--border);
            color: #334155;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .category-pill:hover {
            background: #eef2ff;
            border-color: #818cf8;
            color: var(--primary);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.15);
        }
        
        /* Smooth Global Page Entrance Animation */
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: translateY(12px) scale(0.99);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .container.main-content {
            animation: fadeInScale 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Pulse Badge Animation */
        @keyframes pulseGlow {
            0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(79, 70, 229, 0); }
            100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
        }
        .pulse-badge {
            animation: pulseGlow 2s infinite;
        }

        /* Smooth Alert Entrance */
        .alert {
            animation: fadeInScale 0.3s ease-out forwards;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/">
            <i class="bi bi-tools"></i> Service Connect
        </a>
        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/"><i class="bi bi-house me-1"></i> Home</a>
                </li>
                <?php if ($userRole === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/categories"><i class="bi bi-grid me-1"></i> Manage Categories</a>
                    </li>
                <?php elseif ($userRole === 'provider'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/provider/dashboard"><i class="bi bi-briefcase me-1"></i> Dashboard & Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/provider/bookings"><i class="bi bi-calendar-check me-1"></i> Customer Bookings</a>
                    </li>
                <?php elseif ($userRole === 'customer'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/customer/dashboard"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/customer/search"><i class="bi bi-search me-1"></i> Explore Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/customer/bookings"><i class="bi bi-calendar-event me-1"></i> My Bookings</a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="navbar-nav ms-auto align-items-center gap-2">
                <?php if ($userId): ?>
                    <div class="d-flex align-items-center gap-2 text-white px-2">
                        <i class="bi bi-person-circle fs-5 text-light"></i>
                        <span class="fw-semibold small"><?= htmlspecialchars($userName ?? 'User') ?></span>
                        <span class="badge-role badge-<?= htmlspecialchars($userRole ?? 'customer') ?>">
                            <?= htmlspecialchars(strtoupper($userRole ?? 'customer')) ?>
                        </span>
                    </div>
                    <a class="btn btn-sm btn-outline-light rounded-pill px-3" href="/auth/logout">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                <?php else: ?>
                    <a class="nav-link" href="/auth/login"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a>
                    <a class="btn btn-sm btn-primary-custom" href="/auth/register">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container main-content mt-4 mb-5">
    <?php if ($succ = Session::get('success')): Session::remove('success'); ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div><?= htmlspecialchars($succ) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if ($err = Session::get('error')): Session::remove('error'); ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div><?= htmlspecialchars($err) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
