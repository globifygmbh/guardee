<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="margin-bottom:24px;">
    <h4 style="font-weight:700;">Ausstehende Freigaben</h4>
    <p style="color:#6b7280;font-size:14px;">Kampagnen, die auf deine Prüfung warten.</p>
</div>

<?php if (empty($campaigns)): ?>
    <div class="card"><div class="card-body" style="text-align:center;padding:48px;color:#6b7280;">
        <i class="bi bi-check-circle" style="font-size:48px;color:#22c55e;"></i>
        <p style="margin-top:16px;">Keine ausstehenden Kampagnen. Alles erledigt!</p>
    </div></div>
<?php else: ?>
    <?php foreach ($campaigns as $c): ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div style="flex:1;min-width:200px;">
                        <h5 style="font-weight:600;"><?= esc($c['title']) ?></h5>
                        <div style="font-size:13px;color:#6b7280;margin-bottom:8px;">
                            Budget: <strong style="color:#1D1D1B;"><?= number_format($c['budget'], 2, ',', '.') ?> EUR</strong>
                            &middot; <?= date('d.m.Y', strtotime($c['start_date'])) ?> - <?= date('d.m.Y', strtotime($c['end_date'])) ?>
                        </div>
                        <?php if (!empty($c['description'])): ?>
                            <p style="font-size:14px;color:#374151;margin:0;"><?= esc(mb_substr($c['description'], 0, 200)) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2">
                        <form method="post" action="/campaigns/<?= $c['id'] ?>/approve"><?= csrf_field() ?>
                            <button class="btn btn-success"><i class="bi bi-check-lg"></i> Freigeben</button>
                        </form>
                        <form method="post" action="/campaigns/<?= $c['id'] ?>/reject"><?= csrf_field() ?>
                            <button class="btn btn-outline-danger"><i class="bi bi-x-lg"></i> Ablehnen</button>
                        </form>
                        <a href="/campaigns/<?= $c['id'] ?>" class="btn btn-outline-secondary">Details</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>
