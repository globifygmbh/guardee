<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="margin-bottom:24px;">
    <h4 style="font-weight:700;">Benachrichtigungen</h4>
</div>

<?php if (empty($notifications)): ?>
    <div class="card"><div class="card-body" style="text-align:center;padding:48px;color:#6b7280;">
        <i class="bi bi-bell" style="font-size:48px;"></i>
        <p style="margin-top:16px;">Keine Benachrichtigungen.</p>
    </div></div>
<?php else: ?>
    <div class="card">
        <div class="card-body">
            <?php foreach ($notifications as $n): ?>
                <div class="d-flex justify-content-between align-items-center py-3" style="border-bottom:1px solid #f3f4f6;<?= !$n['is_read'] ? 'background:#f0f5ff;margin:-0 -24px;padding-left:24px;padding-right:24px;' : '' ?>">
                    <div>
                        <div style="font-weight:<?= $n['is_read'] ? '400' : '600' ?>;font-size:14px;"><?= esc($n['title']) ?></div>
                        <div style="font-size:13px;color:#6b7280;"><?= esc($n['message']) ?></div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:4px;"><?= date('d.m.Y H:i', strtotime($n['created_at'])) ?></div>
                    </div>
                    <div class="d-flex gap-2">
                        <?php if (!empty($n['link'])): ?>
                            <a href="<?= esc($n['link']) ?>" class="btn btn-sm btn-outline-primary">Ansehen</a>
                        <?php endif; ?>
                        <?php if (!$n['is_read']): ?>
                            <form method="post" action="/notifications/<?= $n['id'] ?>/read"><?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-secondary">Gelesen</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
