<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="max-width:700px;">
    <div style="margin-bottom:24px;">
        <a href="/campaigns" style="font-size:14px;color:#6b7280;">&larr; Zurück</a>
        <h4 style="font-weight:700;margin-top:8px;">Neue Kampagne erstellen</h4>
    </div>

    <div class="card">
        <div class="card-body" style="padding:32px;">
            <form method="post" action="/campaigns/store" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">Titel *</label>
                    <input type="text" name="title" class="form-control" placeholder="z.B. Summer Fashion Campaign 2025" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Beschreibung</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Beschreibe die Kampagne..."></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Budget (EUR) *</label>
                        <input type="number" name="budget" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Zielgruppe</label>
                        <input type="text" name="target_audience" class="form-control" placeholder="z.B. 18-35, weiblich, fashion-affin">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Länder</label>
                    <input type="text" name="countries" class="form-control" placeholder="z.B. DE, AT, CH">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Startdatum *</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Enddatum *</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Briefing (PDF)</label>
                    <input type="file" name="briefing_file" class="form-control" accept=".pdf">
                    <div style="font-size:12px;color:#6b7280;margin-top:4px;">Lade ein PDF-Briefing für die Influencer hoch.</div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-send"></i> Kampagne einreichen
                </button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
