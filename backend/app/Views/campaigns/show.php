<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="margin-bottom:24px;">
    <a href="/campaigns" style="font-size:14px;color:#6b7280;">&larr; Alle Kampagnen</a>
</div>

<!-- Campaign Header -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <h4 style="font-weight:700;margin:0;"><?= esc($campaign['title']) ?></h4>
                    <span class="badge-status badge-<?= $campaign['status'] ?>"><?= esc($campaign['status']) ?></span>
                </div>
                <?php if ($brand): ?>
                    <div style="font-size:13px;color:#6b7280;">von <strong><?= esc($brand['company_name']) ?></strong></div>
                <?php endif; ?>
            </div>
            <div class="text-end">
                <div style="font-size:28px;font-weight:800;color:#254ccb;"><?= number_format($campaign['budget'], 0, ',', '.') ?> EUR</div>
                <div style="font-size:13px;color:#6b7280;">
                    <?= date('d.m.Y', strtotime($campaign['start_date'])) ?> - <?= date('d.m.Y', strtotime($campaign['end_date'])) ?>
                </div>
            </div>
        </div>

        <?php if ($role === 'admin' && $campaign['status'] === 'pending_approval'): ?>
            <div class="d-flex gap-2 mt-3">
                <form method="post" action="/campaigns/<?= $campaign['id'] ?>/approve"><?= csrf_field() ?>
                    <button class="btn btn-success"><i class="bi bi-check-lg"></i> Freigeben</button>
                </form>
                <form method="post" action="/campaigns/<?= $campaign['id'] ?>/reject"><?= csrf_field() ?>
                    <button class="btn btn-outline-danger"><i class="bi bi-x-lg"></i> Ablehnen</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabOverview" style="font-weight:500;">Übersicht</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabInfluencers" style="font-weight:500;">
            Influencer <span class="badge bg-secondary ms-1"><?= count($invitations) ?></span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabOffers" style="font-weight:500;">
            Angebote <span class="badge bg-secondary ms-1"><?= count($offers) ?></span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabContent" style="font-weight:500;">
            Content <span class="badge bg-secondary ms-1"><?= count($assets) ?></span>
        </button>
    </li>
</ul>

<div class="tab-content">
    <!-- Overview Tab -->
    <div class="tab-pane fade show active" id="tabOverview">
        <div class="card">
            <div class="card-body">
                <?php if (!empty($campaign['description'])): ?>
                    <h6 style="font-weight:600;margin-bottom:8px;">Beschreibung</h6>
                    <p style="font-size:14px;white-space:pre-line;"><?= esc($campaign['description']) ?></p>
                <?php endif; ?>

                <div class="row mt-3">
                    <?php if (!empty($campaign['target_audience'])): ?>
                        <div class="col-md-6 mb-3">
                            <div style="font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Zielgruppe</div>
                            <div style="font-weight:500;"><?= esc($campaign['target_audience']) ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($campaign['countries'])): ?>
                        <div class="col-md-6 mb-3">
                            <div style="font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">Länder</div>
                            <div style="font-weight:500;"><?= esc($campaign['countries']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($campaign['briefing_file'])): ?>
                    <div class="mt-3">
                        <a href="<?= base_url('writable/uploads/briefings/' . $campaign['briefing_file']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-file-pdf"></i> Briefing herunterladen
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Influencers Tab -->
    <div class="tab-pane fade" id="tabInfluencers">
        <?php if ($role === 'admin' && !empty($allInfluencers) && in_array($campaign['status'], ['approved', 'active'])): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h6 style="font-weight:600;margin-bottom:12px;"><i class="bi bi-person-plus"></i> Influencer einladen</h6>
                    <div class="row g-2">
                        <?php foreach ($allInfluencers as $inf): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center gap-3 p-3" style="border:1px solid #e5e5e5;border-radius:12px;">
                                    <div style="width:40px;height:40px;border-radius:50%;background:#254ccb;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:600;">
                                        <?= strtoupper(substr($inf['display_name'] ?? '?', 0, 1)) ?>
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <div style="font-weight:600;font-size:14px;"><?= esc($inf['display_name']) ?></div>
                                        <div style="font-size:12px;color:#6b7280;">
                                            <?= esc($inf['niche'] ?? '') ?> <?= $inf['followers_count'] ? '· ' . number_format($inf['followers_count']) : '' ?>
                                        </div>
                                    </div>
                                    <form method="post" action="/invitations/invite">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="campaign_id" value="<?= $campaign['id'] ?>">
                                        <input type="hidden" name="influencer_id" value="<?= $inf['id'] ?>">
                                        <button class="btn btn-sm btn-primary">Einladen</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <h6 style="font-weight:600;margin-bottom:12px;">Eingeladene Influencer</h6>
                <?php if (empty($invitations)): ?>
                    <p style="color:#6b7280;text-align:center;padding:24px;">Noch keine Influencer eingeladen.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead><tr><th>Name</th><th>Nische</th><th>Follower</th><th>Status</th><th>Aktion</th></tr></thead>
                            <tbody>
                            <?php foreach ($invitations as $inv): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:32px;height:32px;border-radius:50%;background:#254ccb;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;">
                                                <?= strtoupper(substr($inv['display_name'] ?? '?', 0, 1)) ?>
                                            </div>
                                            <span style="font-weight:500;"><?= esc($inv['display_name']) ?></span>
                                        </div>
                                    </td>
                                    <td style="font-size:13px;"><?= esc($inv['niche'] ?? '-') ?></td>
                                    <td style="font-size:13px;"><?= $inv['followers_count'] ? number_format($inv['followers_count']) : '-' ?></td>
                                    <td><span class="badge-status badge-<?= $inv['status'] ?>"><?= esc($inv['status']) ?></span></td>
                                    <td>
                                        <?php if ($role === 'brand' && $inv['status'] === 'interested'): ?>
                                            <?php
                                                $existingOffer = null;
                                                foreach ($offers as $o) { if ($o['invitation_id'] == $inv['id']) { $existingOffer = $o; break; } }
                                            ?>
                                            <?php if (!$existingOffer): ?>
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#offerModal<?= $inv['id'] ?>">Angebot senden</button>
                                                <!-- Offer Modal -->
                                                <div class="modal fade" id="offerModal<?= $inv['id'] ?>" tabindex="-1">
                                                    <div class="modal-dialog"><div class="modal-content" style="border-radius:16px;">
                                                        <div class="modal-header"><h5 class="modal-title">Angebot an <?= esc($inv['display_name']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                        <form method="post" action="/offers/create">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="invitation_id" value="<?= $inv['id'] ?>">
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Betrag (EUR)</label>
                                                                    <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Nachricht</label>
                                                                    <textarea name="message" class="form-control" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Angebot senden</button>
                                                            </div>
                                                        </form>
                                                    </div></div>
                                                </div>
                                            <?php else: ?>
                                                <a href="/offers/<?= $existingOffer['id'] ?>" class="btn btn-sm btn-outline-primary">Angebot ansehen</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Offers Tab -->
    <div class="tab-pane fade" id="tabOffers">
        <div class="card">
            <div class="card-body">
                <?php if (empty($offers)): ?>
                    <p style="color:#6b7280;text-align:center;padding:24px;">Noch keine Angebote.</p>
                <?php else: ?>
                    <?php foreach ($offers as $offer): ?>
                        <div class="d-flex justify-content-between align-items-center py-3" style="border-bottom:1px solid #f3f4f6;">
                            <div>
                                <div style="font-size:20px;font-weight:700;color:#254ccb;"><?= number_format($offer['amount'], 2, ',', '.') ?> <?= esc($offer['currency']) ?></div>
                                <span class="badge-status badge-<?= $offer['status'] ?>"><?= esc($offer['status']) ?></span>
                                <?php if ($offer['terms_accepted']): ?>
                                    <span class="badge bg-success ms-1" style="font-size:11px;">AGB akzeptiert</span>
                                <?php endif; ?>
                            </div>
                            <a href="/offers/<?= $offer['id'] ?>" class="btn btn-sm btn-outline-primary">Details</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Content Tab -->
    <div class="tab-pane fade" id="tabContent">
        <?php if ($role === 'influencer'): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h6 style="font-weight:600;margin-bottom:12px;">Content hochladen</h6>
                    <form method="post" action="/assets/upload" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="campaign_id" value="<?= $campaign['id'] ?>">
                        <div class="upload-zone mb-3" id="uploadZone" onclick="document.getElementById('fileInput').click();">
                            <i class="bi bi-cloud-arrow-up" style="font-size:48px;color:#254ccb;"></i>
                            <p style="font-weight:600;margin:8px 0 4px;">Datei hierher ziehen oder klicken</p>
                            <p style="font-size:13px;color:#6b7280;margin:0;">Bilder, Videos, PDFs (max. 100MB)</p>
                            <input type="file" name="file" id="fileInput" style="display:none;" accept="image/*,video/*,.pdf" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Hochladen</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <h6 style="font-weight:600;margin-bottom:12px;">Hochgeladene Dateien</h6>
                <?php if (empty($assets)): ?>
                    <p style="color:#6b7280;text-align:center;padding:24px;">Noch keine Dateien hochgeladen.</p>
                <?php else: ?>
                    <?php foreach ($assets as $asset): ?>
                        <div class="d-flex justify-content-between align-items-center py-3" style="border-bottom:1px solid #f3f4f6;">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:40px;height:40px;border-radius:10px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-file-earmark" style="font-size:18px;color:#6b7280;"></i>
                                </div>
                                <div>
                                    <div style="font-weight:500;font-size:14px;"><?= esc($asset['file_name']) ?></div>
                                    <div style="font-size:12px;color:#6b7280;">
                                        <?= round($asset['file_size'] / 1024) ?> KB &middot;
                                        <span class="badge-status badge-<?= $asset['status'] ?>"><?= esc($asset['status']) ?></span>
                                    </div>
                                    <?php if (!empty($asset['review_notes'])): ?>
                                        <div style="font-size:12px;color:#f59e0b;margin-top:4px;">
                                            <i class="bi bi-chat-left-text"></i> <?= esc($asset['review_notes']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="/assets/<?= $asset['id'] ?>/download" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i></a>
                                <?php if ($role === 'brand' && $asset['status'] === 'pending_review'): ?>
                                    <form method="post" action="/assets/<?= $asset['id'] ?>/approve"><?= csrf_field() ?>
                                        <button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> Freigeben</button>
                                    </form>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#revisionModal<?= $asset['id'] ?>">
                                        <i class="bi bi-arrow-repeat"></i> Änderung
                                    </button>
                                    <div class="modal fade" id="revisionModal<?= $asset['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog"><div class="modal-content" style="border-radius:16px;">
                                            <div class="modal-header"><h5 class="modal-title">Änderung anfragen</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <form method="post" action="/assets/<?= $asset['id'] ?>/request-revision">
                                                <?= csrf_field() ?>
                                                <div class="modal-body">
                                                    <label class="form-label">Was soll geändert werden?</label>
                                                    <textarea name="review_notes" class="form-control" rows="3" required></textarea>
                                                </div>
                                                <div class="modal-footer"><button type="submit" class="btn btn-warning">Absenden</button></div>
                                            </form>
                                        </div></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Upload zone drag & drop visual feedback
var zone = document.getElementById('uploadZone');
if (zone) {
    var input = document.getElementById('fileInput');
    zone.addEventListener('dragover', function(e) { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', function() { zone.classList.remove('dragover'); });
    zone.addEventListener('drop', function(e) {
        e.preventDefault(); zone.classList.remove('dragover');
        if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; }
    });
    input.addEventListener('change', function() {
        if (input.files.length) { zone.querySelector('p').textContent = input.files[0].name; }
    });
}
</script>
<?= $this->endSection() ?>
