<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="margin-bottom:24px;">
    <h4 style="font-weight:700;">Meine Einladungen</h4>
    <p style="color:#6b7280;font-size:14px;">Kampagneneinladungen, die du erhalten hast.</p>
</div>

<?php if (empty($invitations)): ?>
    <div class="card"><div class="card-body" style="text-align:center;padding:48px;color:#6b7280;">
        <i class="bi bi-envelope" style="font-size:48px;"></i>
        <p style="margin-top:16px;">Noch keine Einladungen. Du wirst benachrichtigt, sobald Brands dich einladen.</p>
    </div></div>
<?php else: ?>
    <?php foreach ($invitations as $inv): ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div style="flex:1;min-width:200px;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <a href="/campaigns/<?= $inv['campaign_id'] ?>" style="font-weight:600;font-size:16px;color:#1D1D1B;">
                                <?= esc($inv['campaign_title']) ?>
                            </a>
                            <span class="badge-status badge-<?= $inv['status'] ?>"><?= esc($inv['status']) ?></span>
                        </div>
                        <div style="font-size:13px;color:#6b7280;">
                            Budget: <strong style="color:#1D1D1B;"><?= number_format($inv['budget'], 2, ',', '.') ?> EUR</strong>
                            &middot; <?= date('d.m.Y', strtotime($inv['start_date'])) ?> - <?= date('d.m.Y', strtotime($inv['end_date'])) ?>
                        </div>
                        <?php if (!empty($inv['campaign_description'])): ?>
                            <p style="font-size:13px;color:#374151;margin-top:8px;"><?= esc(mb_substr($inv['campaign_description'], 0, 150)) ?>...</p>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <?php if ($inv['status'] === 'pending'): ?>
                            <form method="post" action="/invitations/<?= $inv['id'] ?>/respond">
                                <?= csrf_field() ?>
                                <input type="hidden" name="status" value="interested">
                                <button class="btn btn-success">Interesse bekunden</button>
                            </form>
                            <form method="post" action="/invitations/<?= $inv['id'] ?>/respond">
                                <?= csrf_field() ?>
                                <input type="hidden" name="status" value="declined">
                                <button class="btn btn-outline-danger">Ablehnen</button>
                            </form>
                        <?php elseif ($inv['status'] === 'interested' && !empty($inv['offer'])): ?>
                            <a href="/offers/<?= $inv['offer']['id'] ?>" class="btn btn-primary">Angebot ansehen</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>
