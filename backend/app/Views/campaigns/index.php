<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 style="font-weight:700;">Kampagnen</h4>
        <p style="color:#6b7280;font-size:14px;margin:0;">Übersicht aller Kampagnen</p>
    </div>
    <?php if ($role === 'brand' || $role === 'admin'): ?>
        <a href="/campaigns/create" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Neue Kampagne</a>
    <?php endif; ?>
</div>

<?php if (empty($campaigns)): ?>
    <div class="card"><div class="card-body" style="text-align:center;padding:48px;color:#6b7280;">
        Keine Kampagnen vorhanden.
    </div></div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($campaigns as $c): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100" style="cursor:pointer;transition:transform 0.2s,box-shadow 0.2s;"
                     onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)';"
                     onmouseout="this.style.transform='';this.style.boxShadow='';"
                     onclick="window.location='/campaigns/<?= $c['id'] ?>'">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 style="font-weight:600;margin:0;"><?= esc($c['title']) ?></h6>
                            <span class="badge-status badge-<?= $c['status'] ?>"><?= esc($c['status']) ?></span>
                        </div>
                        <div style="font-size:24px;font-weight:700;color:#254ccb;margin-bottom:12px;">
                            <?= number_format($c['budget'], 0, ',', '.') ?> EUR
                        </div>
                        <div style="font-size:13px;color:#6b7280;">
                            <i class="bi bi-calendar3"></i> <?= date('d.m.Y', strtotime($c['start_date'])) ?> - <?= date('d.m.Y', strtotime($c['end_date'])) ?>
                        </div>
                        <?php if (!empty($c['countries'])): ?>
                            <div style="font-size:13px;color:#6b7280;margin-top:4px;">
                                <i class="bi bi-geo-alt"></i> <?= esc($c['countries']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
