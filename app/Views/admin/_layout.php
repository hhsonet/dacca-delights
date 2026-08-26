<?php
/**
 * Admin chrome. Deliberately plain server-rendered HTML + CSS — the storefront's
 * React/CDN runtime is a design artefact for the shop and has no place here.
 */
$nav = [
    ''             => ['Dashboard',    '◧'],
    'products'     => ['Products',     '🥖'],
    'categories'   => ['Categories',   '🗂'],
    'orders'       => ['Orders',       '▤'],
    'customers'    => ['Customers',    '◍'],
    'zones'        => ['Delivery zones', '⌖'],
    'testimonials' => ['Testimonials', '★'],
    'gallery'      => ['Gallery',      '▢'],
];
// Staff management is administrators-only, so don't advertise it to staff.
if (($adminRole ?? '') === 'admin') {
    $nav['users'] = ['Staff users', '⚿'];
}
$flash = session()->getFlashdata('flash');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($title ?? 'Admin') ?> · Dacca Delights</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    --plum:#561530; --berry:#9E1C60; --gold:#F5AD18; --cream:#FFF9F1;
    --ink:#2B171F; --muted:#75666B; --line:#EADFE2; --ok:#17693F; --bad:#B3261E;
  }
  *{box-sizing:border-box}
  body{margin:0;background:#F7F4F0;color:var(--ink);
       font-family:'Plus Jakarta Sans',system-ui,sans-serif;font-size:14px}
  a{color:var(--berry);text-decoration:none}
  .shell{display:flex;min-height:100vh}
  /* ---- sidebar ---- */
  .side{width:236px;flex:none;background:var(--plum);color:var(--cream);
        display:flex;flex-direction:column;position:sticky;top:0;height:100vh}
  .brand{padding:20px 18px;border-bottom:1px solid rgba(255,249,241,.14);
         font-weight:800;letter-spacing:.02em}
  .brand small{display:block;font-weight:500;font-size:11px;letter-spacing:.18em;
               color:var(--gold);margin-top:3px}
  .side nav{padding:10px;display:flex;flex-direction:column;gap:2px;overflow-y:auto}
  .side nav a{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;
              color:rgba(255,249,241,.82);font-weight:500}
  .side nav a:hover{background:rgba(255,249,241,.10);color:#fff}
  .side nav a.on{background:var(--gold);color:var(--plum);font-weight:700}
  .side .foot{margin-top:auto;padding:14px 16px;border-top:1px solid rgba(255,249,241,.14);
              font-size:12px;color:rgba(255,249,241,.72)}
  .side .foot a{color:var(--gold);font-weight:600}
  /* ---- main ---- */
  .main{flex:1;min-width:0;display:flex;flex-direction:column}
  .top{background:#fff;border-bottom:1px solid var(--line);padding:14px 22px;
       display:flex;align-items:center;justify-content:space-between;gap:14px}
  .top h1{margin:0;font-size:19px;font-weight:700}
  .wrap{padding:22px;flex:1}
  /* ---- components ---- */
  .card{background:#fff;border:1px solid var(--line);border-radius:14px}
  .card .hd{padding:14px 16px;border-bottom:1px solid var(--line);
            display:flex;align-items:center;justify-content:space-between;gap:12px}
  .card .hd h2{margin:0;font-size:15px;font-weight:700}
  .grid{display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(180px,1fr))}
  .stat{background:#fff;border:1px solid var(--line);border-radius:14px;padding:16px}
  .stat .k{font-size:11px;letter-spacing:.16em;color:var(--muted);font-weight:700}
  .stat .v{font-size:26px;font-weight:800;color:var(--plum);margin-top:6px}
  table{width:100%;border-collapse:collapse}
  th,td{padding:11px 14px;text-align:left;border-bottom:1px solid var(--line);vertical-align:middle}
  th{font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);font-weight:700}
  tbody tr:hover{background:#FFFCF7}
  .tablewrap{overflow-x:auto}
  .btn{display:inline-flex;align-items:center;gap:7px;border:0;border-radius:10px;
       padding:9px 15px;font:inherit;font-weight:700;cursor:pointer;
       background:var(--gold);color:var(--plum)}
  .btn:hover{background:var(--berry);color:#fff}
  .btn.ghost{background:#fff;border:1px solid var(--line);color:var(--plum);font-weight:600}
  .btn.ghost:hover{border-color:var(--plum);background:#fff;color:var(--plum)}
  .btn.danger{background:#fff;border:1px solid #E4B4B0;color:var(--bad);font-weight:600}
  .btn.danger:hover{background:var(--bad);color:#fff}
  .btn.sm{padding:6px 11px;font-size:12.5px}
  .pill{display:inline-block;padding:3px 10px;border-radius:999px;font-size:11.5px;font-weight:700}
  label{display:block;font-size:12px;font-weight:700;color:var(--muted);
        letter-spacing:.04em;margin-bottom:5px}
  input[type=text],input[type=email],input[type=password],input[type=number],
  input[type=date],select,textarea{
    width:100%;padding:10px 12px;border:1px solid var(--line);border-radius:10px;
    font:inherit;background:#fff;color:var(--ink)}
  input:focus,select:focus,textarea:focus{outline:2px solid var(--berry);outline-offset:1px;border-color:transparent}
  .field{margin-bottom:14px}
  .row{display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr))}
  .err{color:var(--bad);font-size:12.5px;margin-top:5px}
  .flash{padding:12px 15px;border-radius:11px;margin-bottom:16px;font-weight:600}
  .flash.ok{background:#E8F5EE;color:var(--ok);border:1px solid #B7DFC8}
  .flash.err{background:#FDECEA;color:var(--bad);border:1px solid #E4B4B0}
  .muted{color:var(--muted)}
  .empty{padding:44px 20px;text-align:center;color:var(--muted)}
  .actions{display:flex;gap:7px;flex-wrap:wrap}
  @media(max-width:860px){
    .shell{flex-direction:column}
    .side{width:auto;height:auto;position:static;flex-direction:row;flex-wrap:wrap;align-items:center}
    .side nav{flex-direction:row;flex-wrap:wrap;flex:1}
    .side .foot{margin:0;border:0}
  }
</style>
</head>
<body>
<div class="shell">
  <aside class="side">
    <div class="brand">Dacca Delights<small>ADMIN</small></div>
    <nav>
      <?php foreach ($nav as $seg => [$label, $icon]): ?>
        <a href="<?= base_url('admin' . ($seg ? '/' . $seg : '')) ?>"
           class="<?= ($active ?? '') === $seg ? 'on' : '' ?>">
          <span aria-hidden="true"><?= $icon ?></span><?= esc($label) ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="foot">
      <div><strong><?= esc($adminName ?? '') ?></strong></div>
      <div style="opacity:.75;margin:2px 0 8px"><?= esc($adminRole ?? '') ?></div>
      <a href="<?= base_url() ?>" target="_blank" rel="noopener">View site ↗</a> ·
      <a href="<?= base_url('admin/logout') ?>">Sign out</a>
    </div>
  </aside>

  <div class="main">
    <header class="top">
      <h1><?= esc($title ?? '') ?></h1>
      <?= $headerActions ?? '' ?>
    </header>
    <div class="wrap">
      <?php if ($flash): ?>
        <div class="flash <?= $flash['type'] === 'ok' ? 'ok' : 'err' ?>"><?= esc($flash['message']) ?></div>
      <?php endif; ?>
      <?= $content ?? '' ?>
    </div>
  </div>
</div>
</body>
</html>
