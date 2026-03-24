<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Guardee' ?> - Guardee Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #254ccb;
            --primary-light: #3a5fd9;
            --bg: #eeeae9;
            --dark: #1D1D1B;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --sidebar-w: 260px;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; color: var(--dark); }
        a { text-decoration: none; color: inherit; }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-w); background: var(--dark); position: fixed;
            top: 0; left: 0; bottom: 0; display: flex; flex-direction: column; z-index: 999;
        }
        .sidebar-logo { padding: 24px; font-size: 24px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
        .sidebar-nav { flex: 1; padding: 8px 12px; display: flex; flex-direction: column; gap: 4px; }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 8px;
            font-size: 14px; font-weight: 500; color: rgba(255,255,255,0.6); transition: all 0.2s;
        }
        .sidebar-nav a:hover { color: #fff; background: rgba(255,255,255,0.08); }
        .sidebar-nav a.active { color: #fff; background: var(--primary); }
        .sidebar-user {
            padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.1);
            display: flex; align-items: center; gap: 12px;
        }
        .sidebar-user .avatar {
            width: 36px; height: 36px; border-radius: 50%; background: var(--primary);
            display: flex; align-items: center; justify-content: center; color: #fff;
            font-weight: 600; font-size: 14px; flex-shrink: 0;
        }
        .sidebar-user .name { color: #fff; font-size: 13px; font-weight: 600; }
        .sidebar-user .role { color: rgba(255,255,255,0.5); font-size: 11px; text-transform: capitalize; }

        /* Main */
        .main { margin-left: var(--sidebar-w); min-height: 100vh; }
        .topbar {
            height: 64px; background: #fff; border-bottom: 1px solid #e5e5e5;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 32px; position: sticky; top: 0; z-index: 100;
        }
        .content { padding: 32px; }

        /* Cards */
        .card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
        .card-body { padding: 24px; }

        /* Buttons */
        .btn { border-radius: 8px; font-weight: 500; padding: 10px 20px; font-size: 14px; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-light); border-color: var(--primary-light); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37,76,203,0.3); }
        .btn-success { background: var(--success); border-color: var(--success); }
        .btn-danger { background: var(--danger); border-color: var(--danger); }
        .btn-warning { background: var(--warning); border-color: var(--warning); color: #fff; }

        /* Status badges */
        .badge-status { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-draft { background: #9ca3af; color: #fff; }
        .badge-pending_approval { background: #f59e0b; color: #fff; }
        .badge-approved { background: #254ccb; color: #fff; }
        .badge-active { background: #22c55e; color: #fff; }
        .badge-completed { background: #8b5cf6; color: #fff; }
        .badge-cancelled { background: #ef4444; color: #fff; }
        .badge-pending { background: #f59e0b; color: #fff; }
        .badge-interested { background: #3b82f6; color: #fff; }
        .badge-declined { background: #ef4444; color: #fff; }
        .badge-accepted { background: #22c55e; color: #fff; }
        .badge-revision_requested { background: #f59e0b; color: #fff; }
        .badge-pending_review { background: #6366f1; color: #fff; }
        .badge-revised { background: #8b5cf6; color: #fff; }

        /* KPI cards */
        .kpi-card { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
        .kpi-card .kpi-value { font-size: 32px; font-weight: 800; color: var(--primary); }
        .kpi-card .kpi-label { font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500; }

        /* Forms */
        .form-control, .form-select { border-radius: 8px; border: 1px solid #e5e5e5; padding: 10px 14px; font-size: 14px; }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,76,203,0.1); }
        .form-label { font-weight: 500; font-size: 13px; color: #6b7280; margin-bottom: 6px; }

        /* Table */
        .table th { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; font-weight: 600; border-bottom: 2px solid #e5e5e5; }
        .table td { vertical-align: middle; font-size: 14px; }

        /* Upload zone */
        .upload-zone {
            border: 2px dashed #d1d5db; border-radius: 16px; padding: 48px; text-align: center;
            cursor: pointer; transition: all 0.2s; background: #fafafa;
        }
        .upload-zone:hover { border-color: var(--primary); background: rgba(37,76,203,0.04); }
        .upload-zone.dragover { border-color: var(--primary); background: rgba(37,76,203,0.08); }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .content { padding: 16px; }
            .topbar { padding: 0 16px; }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">guardee</div>
    <nav class="sidebar-nav">
        <?php $uri = service('uri')->getPath(); $role = session()->get('user_role'); ?>

        <a href="/dashboard" class="<?= $uri === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid"></i> Dashboard
        </a>
        <a href="/campaigns" class="<?= str_starts_with($uri, 'campaigns') && $uri !== 'campaigns/pending' && $uri !== 'campaigns/create' ? 'active' : '' ?>">
            <i class="bi bi-megaphone"></i> Kampagnen
        </a>

        <?php if ($role === 'admin'): ?>
            <a href="/campaigns/pending" class="<?= $uri === 'campaigns/pending' ? 'active' : '' ?>">
                <i class="bi bi-clock-history"></i> Freigaben
            </a>
            <a href="/users" class="<?= str_starts_with($uri, 'users') ? 'active' : '' ?>">
                <i class="bi bi-people"></i> Benutzer
            </a>
        <?php endif; ?>

        <?php if ($role === 'brand'): ?>
            <a href="/campaigns/create" class="<?= $uri === 'campaigns/create' ? 'active' : '' ?>">
                <i class="bi bi-plus-circle"></i> Kampagne erstellen
            </a>
        <?php endif; ?>

        <?php if ($role === 'influencer'): ?>
            <a href="/invitations" class="<?= $uri === 'invitations' ? 'active' : '' ?>">
                <i class="bi bi-envelope"></i> Einladungen
            </a>
        <?php endif; ?>

        <a href="/notifications" class="<?= $uri === 'notifications' ? 'active' : '' ?>">
            <i class="bi bi-bell"></i> Benachrichtigungen
        </a>
    </nav>
    <div class="sidebar-user">
        <div class="avatar"><?= strtoupper(substr(session()->get('user_name') ?: 'U', 0, 1)) ?></div>
        <div style="flex:1;min-width:0;">
            <div class="name" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= esc(session()->get('user_name')) ?></div>
            <div class="role"><?= esc(session()->get('user_role')) ?></div>
        </div>
        <a href="/logout" title="Logout" style="color:rgba(255,255,255,0.5);"><i class="bi bi-box-arrow-right"></i></a>
    </div>
</aside>

<!-- Main -->
<div class="main">
    <header class="topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="btn btn-link d-md-none p-0" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <i class="bi bi-list" style="font-size:22px;color:var(--dark);"></i>
            </button>
        </div>
        <div style="display:flex;align-items:center;gap:16px;">
            <span style="font-size:14px;font-weight:500;"><?= esc(session()->get('user_name')) ?></span>
            <a href="/logout" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">Logout</a>
        </div>
    </header>

    <div class="content">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" style="border-radius:12px;">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" style="border-radius:12px;">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
