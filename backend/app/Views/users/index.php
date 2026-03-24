<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 style="font-weight:700;">Benutzerverwaltung</h4>
        <p style="color:#6b7280;font-size:14px;margin:0;">Alle registrierten Benutzer</p>
    </div>
    <a href="/users/influencers" class="btn btn-outline-primary">Influencer-Profile</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Name</th><th>E-Mail</th><th>Rolle</th><th>Status</th><th>Registriert</th></tr></thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td style="font-weight:500;"><?= esc($u['first_name'] . ' ' . $u['last_name']) ?></td>
                        <td style="font-size:13px;"><?= esc($u['email']) ?></td>
                        <td><span class="badge bg-secondary" style="font-size:11px;"><?= esc($u['role_label']) ?></span></td>
                        <td><span class="badge-status badge-<?= $u['status'] ?>"><?= esc($u['status']) ?></span></td>
                        <td style="font-size:13px;color:#6b7280;"><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
