<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="max-width:600px;margin:0 auto;">
    <a href="javascript:history.back();" style="font-size:14px;color:#6b7280;">&larr; Zurück</a>
    <h4 style="font-weight:700;margin-top:8px;margin-bottom:24px;">Angebotsdetails</h4>

    <div class="card">
        <div class="card-body" style="padding:32px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <div style="font-size:13px;color:#6b7280;">Kampagne</div>
                    <a href="/campaigns/<?= $campaign['id'] ?>" style="font-weight:600;font-size:16px;"><?= esc($campaign['title']) ?></a>
                </div>
                <span class="badge-status badge-<?= $offer['status'] ?>"><?= esc($offer['status']) ?></span>
            </div>

            <div style="background:#f9fafb;border-radius:12px;padding:24px;text-align:center;margin-bottom:24px;">
                <div style="font-size:13px;color:#6b7280;">Angebotsbetrag</div>
                <div style="font-size:36px;font-weight:800;color:#254ccb;"><?= number_format($offer['amount'], 2, ',', '.') ?> <?= esc($offer['currency']) ?></div>
            </div>

            <?php if (!empty($offer['message'])): ?>
                <div style="margin-bottom:24px;">
                    <div style="font-size:13px;color:#6b7280;margin-bottom:4px;">Nachricht</div>
                    <p style="font-size:14px;"><?= esc($offer['message']) ?></p>
                </div>
            <?php endif; ?>

            <!-- Brand: Update offer -->
            <?php if ($role === 'brand' && $offer['status'] === 'pending'): ?>
                <form method="post" action="/offers/<?= $offer['id'] ?>/update" class="mb-3">
                    <?= csrf_field() ?>
                    <div class="row g-2 mb-2">
                        <div class="col">
                            <input type="number" name="amount" class="form-control" value="<?= $offer['amount'] ?>" step="0.01" min="0">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-primary">Aktualisieren</button>
                        </div>
                    </div>
                    <textarea name="message" class="form-control" rows="2" placeholder="Nachricht (optional)"><?= esc($offer['message']) ?></textarea>
                </form>
            <?php endif; ?>

            <!-- Influencer: Accept / Decline -->
            <?php if ($role === 'influencer' && $offer['status'] === 'pending'): ?>
                <div class="d-flex gap-3 mb-3">
                    <form method="post" action="/offers/<?= $offer['id'] ?>/accept" class="flex-fill"><?= csrf_field() ?>
                        <button class="btn btn-success w-100">Angebot annehmen</button>
                    </form>
                    <form method="post" action="/offers/<?= $offer['id'] ?>/decline" class="flex-fill"><?= csrf_field() ?>
                        <button class="btn btn-outline-danger w-100">Ablehnen</button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Influencer: Accept Terms (AGB) -->
            <?php if ($role === 'influencer' && $offer['status'] === 'accepted' && !$offer['terms_accepted']): ?>
                <div style="border:2px solid #254ccb;border-radius:12px;padding:24px;margin-top:16px;">
                    <h6 style="font-weight:600;">AGB / Nutzungsbedingungen</h6>
                    <p style="font-size:13px;color:#6b7280;">
                        Mit der Bestätigung stimmst du den Plattform-Nutzungsbedingungen für diese Kooperation zu.
                        Bitte stelle sicher, dass du das Kampagnen-Briefing gelesen und verstanden hast.
                    </p>
                    <form method="post" action="/offers/<?= $offer['id'] ?>/accept-terms">
                        <?= csrf_field() ?>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="termsCheck" required>
                            <label class="form-check-label" for="termsCheck" style="font-size:14px;">
                                Ich akzeptiere die AGB und Nutzungsbedingungen.
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Bestätigen & AGB akzeptieren</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($offer['terms_accepted']): ?>
                <div class="alert alert-success" style="border-radius:12px;margin-top:16px;">
                    <i class="bi bi-check-circle"></i> AGB akzeptiert. Du kannst jetzt Content hochladen.
                    <a href="/campaigns/<?= $campaign['id'] ?>" class="btn btn-sm btn-outline-success ms-2">Zur Kampagne</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
