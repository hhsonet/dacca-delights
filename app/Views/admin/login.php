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
  /* Password field + its reveal toggle. */
  .pw{position:relative}
  .pw input{padding-right:46px}
  .pw button{position:absolute;top:0;right:0;width:44px;height:46px;padding:0;
             background:none;border:0;border-radius:0 11px 11px 0;cursor:pointer;
             color:#75666B;display:flex;align-items:center;justify-content:center}
  .pw button:hover{background:none;color:var(--berry)}
  .pw button:focus-visible{outline:2px solid var(--berry);outline-offset:-2px}
  .pw svg{width:19px;height:19px;display:block}
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
    <div class="pw">
      <input type="password" id="password" name="password" required autocomplete="current-password">
      <button type="button" id="pwToggle" aria-label="Show password" aria-pressed="false" title="Show password">
        <svg id="pwEye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M2 12s3.6-6.5 10-6.5S22 12 22 12s-3.6 6.5-10 6.5S2 12 2 12z"></path>
          <circle cx="12" cy="12" r="3"></circle>
        </svg>
      </button>
    </div>

    <button type="submit">Sign in</button>
    <a class="back" href="<?= base_url() ?>">← Back to the shop</a>
  </form>

<script>
(function () {
  var input  = document.getElementById('password');
  var toggle = document.getElementById('pwToggle');
  var eye    = document.getElementById('pwEye');
  if (!input || !toggle) return;

  var OPEN   = '<path d="M2 12s3.6-6.5 10-6.5S22 12 22 12s-3.6 6.5-10 6.5S2 12 2 12z"></path>'
             + '<circle cx="12" cy="12" r="3"></circle>';
  var CLOSED = '<path d="M3 3l18 18"></path>'
             + '<path d="M10.6 6.1A9.6 9.6 0 0 1 12 6c6.4 0 10 6 10 6a17 17 0 0 1-3.4 4"></path>'
             + '<path d="M6.5 8.2A17 17 0 0 0 2 12s3.6 6.5 10 6.5a9.9 9.9 0 0 0 4-.8"></path>'
             + '<path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"></path>';

  toggle.addEventListener('click', function () {
    var show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    eye.innerHTML = show ? CLOSED : OPEN;
    toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
    var label = show ? 'Hide password' : 'Show password';
    toggle.setAttribute('aria-label', label);
    toggle.setAttribute('title', label);
    // Keep the caret where it was so typing can continue uninterrupted.
    input.focus();
    var end = input.value.length;
    try { input.setSelectionRange(end, end); } catch (e) {}
  });

  // Never leave the password on screen after submitting.
  var form = toggle.closest('form');
  if (form) {
    form.addEventListener('submit', function () { input.type = 'password'; });
  }
})();
</script>
</body>
</html>
