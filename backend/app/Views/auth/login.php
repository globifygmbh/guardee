<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<form method="post" action="/login">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">E-Mail</label>
        <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required autofocus>
    </div>
    <div class="mb-4">
        <label class="form-label">Passwort</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary w-100 mb-3">Einloggen</button>
    <div class="text-center" style="font-size:14px;">
        Noch kein Konto? <a href="/register" style="color:#254ccb;font-weight:500;">Registrieren</a>
    </div>
</form>
<?= $this->endSection() ?>
