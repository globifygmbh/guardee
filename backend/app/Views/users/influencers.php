<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="margin-bottom:24px;">
    <a href="/users" style="font-size:14px;color:#6b7280;">&larr; Alle Benutzer</a>
    <h4 style="font-weight:700;margin-top:8px;">Influencer-Profile</h4>
</div>

<?php if (empty($influencers)): ?>
    <div class="card"><div class="card-body" style="text-align:center;padding:48px;color:#6b7280;">
        Keine Influencer registriert.
    </div></div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($influencers as $inf): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body d-flex gap-3">
                        <div style="width:48px;height:48px;border-radius:50%;background:#254ccb;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;flex-shrink:0;">
                            <?= strtoupper(substr($inf['display_name'] ?? '?', 0, 1)) ?>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <h6 style="font-weight:600;margin-bottom:2px;"><?= esc($inf['display_name']) ?></h6>
                            <?php if ($inf['niche']): ?>
                                <span class="badge bg-light text-dark" style="font-size:11px;font-weight:500;"><?= esc($inf['niche']) ?></span>
                            <?php endif; ?>
                            <div style="font-size:13px;color:#6b7280;margin-top:8px;">
                                <?php if ($inf['instagram_handle']): ?>
                                    <div><i class="bi bi-instagram"></i> <?= esc($inf['instagram_handle']) ?></div>
                                <?php endif; ?>
                                <?php if ($inf['country']): ?>
                                    <div><i class="bi bi-geo-alt"></i> <?= esc($inf['country']) ?></div>
                                <?php endif; ?>
                                <?php if ($inf['followers_count']): ?>
                                    <div style="font-weight:600;color:#1D1D1B;margin-top:4px;">
                                        <?= number_format($inf['followers_count']) ?> Follower
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
