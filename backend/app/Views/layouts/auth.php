<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Login' ?> - Guardee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #eeeae9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 48px;
            width: 100%;
            max-width: 440px;
        }
        .auth-logo {
            font-size: 32px;
            font-weight: 800;
            color: #1D1D1B;
            letter-spacing: -0.5px;
            text-align: center;
            margin-bottom: 8px;
        }
        .auth-subtitle {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 32px;
        }
        .btn-primary { background: #254ccb; border-color: #254ccb; border-radius: 8px; font-weight: 500; padding: 12px; }
        .btn-primary:hover { background: #3a5fd9; border-color: #3a5fd9; }
        .form-control { border-radius: 8px; border: 1px solid #e5e5e5; padding: 12px 14px; font-size: 14px; }
        .form-control:focus { border-color: #254ccb; box-shadow: 0 0 0 3px rgba(37,76,203,0.1); }
        .form-label { font-weight: 500; font-size: 13px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-logo">guardee</div>
        <div class="auth-subtitle"><?= $subtitle ?? 'Influencer Marketing Platform' ?></div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" style="border-radius:10px;font-size:14px;">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>
</body>
</html>
