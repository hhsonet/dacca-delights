<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin sign in · Dacca Delights</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{--plum:#561530;--berry:#9E1C60;--gold:#F5AD18;--cream:#FFF9F1;--line:#EADFE2;--bad:#B3261E}
  *{box-sizing:border-box}
  body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;
       background:var(--plum);font-family:'Plus Jakarta Sans',system-ui,sans-serif;padding:20px}
  .box{width:100%;max-width:380px;background:var(--cream);border-radius:20px;padding:30px;
       box-shadow:0 24px 60px rgba(0,0,0,.28)}
  h1{margin:0 0 4px;font-size:21px;color:var(--plum)}
  p.sub{margin:0 0 22px;font-size:13px;color:#75666B}
  label{display:block;font-size:12px;font-weight:700;color:#75666B;margin-bottom:5px}
  input{width:100%;padding:12px 13px;border:1px solid var(--line);border-radius:11px;
        font:inherit;background:#fff;margin-bottom:14px}
  input:focus{outline:2px solid var(--berry);outline-offset:1px;border-color:transparent}
  button{width:100%;padding:13px;border:0;border-radius:12px;background:var(--gold);
         color:var(--plum);font:inherit;font-weight:800;cursor:pointer}
  button:hover{background:var(--berry);color:#fff}
  .err{background:#FDECEA;color:var(--bad);border:1px solid #E4B4B0;
       padding:11px 13px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px}
  .back{display:block;text-align:center;margin-top:18px;font-size:12.5px;color:#75666B}
</style>
</head>
<body>
  <form class="box" method="post" action="<?= base_url('admin/login') ?>">
    <?= csrf_field() ?>
    <h1>Dacca Delights</h1>
    <p class="sub">Staff sign in</p>

    <?php if (session('error')): ?>
      <div class="err"><?= esc(session('error')) ?></div>
    <?php endif; ?>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required autocomplete="username"
           value="<?= esc(old('email') ?? '') ?>">

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required autocomplete="current-password">

    <button type="submit">Sign in</button>
    <a class="back" href="<?= base_url() ?>">← Back to the shop</a>
  </form>
</body>
</html>
