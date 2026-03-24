<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<form method="post" action="/register" id="registerForm">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label class="form-label">Ich bin ein...</label>
        <div class="d-flex gap-2">
            <label class="flex-fill">
                <input type="radio" name="role" value="brand" class="btn-check" id="roleBrand" required>
                <div class="btn btn-outline-secondary w-100" onclick="document.getElementById('roleBrand').checked=true; toggleFields();" id="btnBrand">
                    <i class="bi bi-building"></i> Unternehmen
                </div>
            </label>
            <label class="flex-fill">
                <input type="radio" name="role" value="influencer" class="btn-check" id="roleInfluencer">
                <div class="btn btn-outline-secondary w-100" onclick="document.getElementById('roleInfluencer').checked=true; toggleFields();" id="btnInfluencer">
                    <i class="bi bi-person-video3"></i> Influencer
                </div>
            </label>
        </div>
    </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">Vorname</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">Nachname</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">E-Mail</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Passwort</label>
        <input type="password" name="password" class="form-control" minlength="6" required>
    </div>

    <!-- Brand fields -->
    <div id="brandFields" style="display:none;">
        <div class="mb-3">
            <label class="form-label">Firmenname</label>
            <input type="text" name="company_name" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Branche</label>
            <input type="text" name="industry" class="form-control" placeholder="z.B. Fashion, Tech, Food...">
        </div>
    </div>

    <!-- Influencer fields -->
    <div id="influencerFields" style="display:none;">
        <div class="mb-3">
            <label class="form-label">Display Name</label>
            <input type="text" name="display_name" class="form-control">
        </div>
        <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label">Nische</label>
                <input type="text" name="niche" class="form-control" placeholder="z.B. Fashion, Fitness...">
            </div>
            <div class="col-6 mb-3">
                <label class="form-label">Instagram</label>
                <input type="text" name="instagram_handle" class="form-control" placeholder="@handle">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Land</label>
            <select name="country" class="form-select">
                <option value="">Auswählen...</option>
                <option value="DE">Deutschland</option>
                <option value="AT">Österreich</option>
                <option value="CH">Schweiz</option>
                <option value="US">USA</option>
                <option value="UK">UK</option>
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">Registrieren</button>
    <div class="text-center" style="font-size:14px;">
        Bereits registriert? <a href="/login" style="color:#254ccb;font-weight:500;">Einloggen</a>
    </div>
</form>

<script>
function toggleFields() {
    var role = document.querySelector('input[name="role"]:checked');
    document.getElementById('brandFields').style.display = role && role.value === 'brand' ? 'block' : 'none';
    document.getElementById('influencerFields').style.display = role && role.value === 'influencer' ? 'block' : 'none';
    document.getElementById('btnBrand').className = 'btn w-100 ' + (role && role.value === 'brand' ? 'btn-primary' : 'btn-outline-secondary');
    document.getElementById('btnInfluencer').className = 'btn w-100 ' + (role && role.value === 'influencer' ? 'btn-primary' : 'btn-outline-secondary');
}
</script>
<?= $this->endSection() ?>
