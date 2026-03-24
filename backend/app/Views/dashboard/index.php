<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="margin-bottom:24px;">
    <h4 style="font-weight:700;">Willkommen zurück, <?= esc($user_name) ?></h4>
    <p style="color:#6b7280;font-size:14px;">Hier ist dein Überblick.</p>
</div>

<?php if ($role === 'admin'): ?>
    <!-- Admin Dashboard -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-value"><?= $pending_count ?></div>
                <div class="kpi-label">Ausstehende Freigaben</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-value"><?= $active_count ?></div>
                <div class="kpi-label">Aktive Kampagnen</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-value"><?= $total_campaigns ?></div>
                <div class="kpi-label">Kampagnen Total</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-value"><?= $total_users ?></div>
                <div class="kpi-label">Benutzer</div>
            </div>
        </div>
    </div>

    <?php if ($pending_count > 0): ?>
        <a href="/campaigns/pending" class="btn btn-warning mb-4">
            <i class="bi bi-clock-history"></i> <?= $pending_count ?> Kampagnen warten auf Freigabe
        </a>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <h6 style="font-weight:600;margin-bottom:16px;">Neueste Kampagnen</h6>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Titel</th><th>Status</th><th>Budget</th><th>Erstellt</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($recent_campaigns as $c): ?>
                        <tr>
                            <td style="font-weight:500;"><?= esc($c['title']) ?></td>
                            <td><span class="badge-status badge-<?= $c['status'] ?>"><?= esc($c['status']) ?></span></td>
                            <td><?= number_format($c['budget'], 2, ',', '.') ?> EUR</td>
                            <td style="font-size:13px;color:#6b7280;"><?= date('d.m.Y', strtotime($c['created_at'])) ?></td>
                            <td><a href="/campaigns/<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary">Details</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($role === 'brand'): ?>
    <!-- Brand Dashboard -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="kpi-card">
                <div class="kpi-value"><?= $total_campaigns ?></div>
                <div class="kpi-label">Meine Kampagnen</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="kpi-card">
                <div class="kpi-value"><?= $active_count ?></div>
                <div class="kpi-label">Aktiv</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="kpi-card">
                <div class="kpi-value"><?= $pending_count ?></div>
                <div class="kpi-label">Warten auf Freigabe</div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 style="font-weight:600;">Meine Kampagnen</h6>
        <a href="/campaigns/create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Neue Kampagne</a>
    </div>
    <div class="card">
        <div class="card-body">
            <?php if (empty($my_campaigns)): ?>
                <p style="text-align:center;color:#6b7280;padding:32px;">Noch keine Kampagnen erstellt.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Titel</th><th>Status</th><th>Budget</th><th>Zeitraum</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($my_campaigns as $c): ?>
                            <tr>
                                <td style="font-weight:500;"><?= esc($c['title']) ?></td>
                                <td><span class="badge-status badge-<?= $c['status'] ?>"><?= esc($c['status']) ?></span></td>
                                <td><?= number_format($c['budget'], 2, ',', '.') ?> EUR</td>
                                <td style="font-size:13px;"><?= date('d.m.Y', strtotime($c['start_date'])) ?> - <?= date('d.m.Y', strtotime($c['end_date'])) ?></td>
                                <td><a href="/campaigns/<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary">Details</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php elseif ($role === 'influencer'): ?>
    <!-- Influencer Dashboard -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6">
            <div class="kpi-card">
                <div class="kpi-value"><?= $pending_invites ?></div>
                <div class="kpi-label">Offene Einladungen</div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="kpi-card">
                <div class="kpi-value"><?= $active_collabs ?></div>
                <div class="kpi-label">Aktive Kooperationen</div>
            </div>
        </div>
    </div>

    <h6 style="font-weight:600;margin-bottom:12px;">Neueste Einladungen</h6>
    <?php if (empty($invitations)): ?>
        <div class="card"><div class="card-body" style="text-align:center;color:#6b7280;padding:32px;">
            Noch keine Einladungen. Du wirst benachrichtigt, sobald Brands dich einladen.
        </div></div>
    <?php else: ?>
        <?php foreach ($invitations as $inv): ?>
            <div class="card mb-3">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <a href="/campaigns/<?= $inv['campaign_id'] ?>" style="font-weight:600;font-size:16px;">
                            <?= esc($inv['campaign_title']) ?>
                        </a>
                        <span class="badge-status badge-<?= $inv['status'] ?> ms-2"><?= esc($inv['status']) ?></span>
                        <div style="font-size:13px;color:#6b7280;margin-top:4px;">
                            Budget: <strong><?= number_format($inv['budget'], 2, ',', '.') ?> EUR</strong>
                            &middot; <?= date('d.m.Y', strtotime($inv['start_date'])) ?> - <?= date('d.m.Y', strtotime($inv['end_date'])) ?>
                        </div>
                    </div>
                    <?php if ($inv['status'] === 'pending'): ?>
                        <div class="d-flex gap-2">
                            <form method="post" action="/invitations/<?= $inv['id'] ?>/respond">
                                <?= csrf_field() ?>
                                <input type="hidden" name="status" value="interested">
                                <button class="btn btn-success btn-sm">Interesse</button>
                            </form>
                            <form method="post" action="/invitations/<?= $inv['id'] ?>/respond">
                                <?= csrf_field() ?>
                                <input type="hidden" name="status" value="declined">
                                <button class="btn btn-outline-danger btn-sm">Ablehnen</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

<?php endif; ?>

<!-- Notifications -->
<?php if (!empty($notifications)): ?>
    <div class="card mt-4">
        <div class="card-body">
            <h6 style="font-weight:600;margin-bottom:12px;"><i class="bi bi-bell"></i> Neue Benachrichtigungen</h6>
            <?php foreach ($notifications as $n): ?>
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid #f3f4f6;">
                    <div>
                        <div style="font-weight:500;font-size:14px;"><?= esc($n['title']) ?></div>
                        <div style="font-size:12px;color:#6b7280;"><?= esc($n['message']) ?></div>
                    </div>
                    <span style="font-size:11px;color:#9ca3af;"><?= date('d.m. H:i', strtotime($n['created_at'])) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
