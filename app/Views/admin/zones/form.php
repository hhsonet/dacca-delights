<?php
$isEdit = $row !== null;
$v = static fn(string $k, $d = '') => esc(old($k, $row[$k] ?? $d) ?? '', 'attr');
$action = $isEdit ? base_url('admin/zones/' . $row['id']) : base_url('admin/zones');
ob_start(); ?>
<form method="post" action="<?= $action ?>" class="card" style="padding:18px;max-width:560px">
  <?= csrf_field() ?>
  <div class="field"><label for="name">Area name</label>
    <input type="text" id="name" name="name" required value="<?= $v('name') ?>"></div>
  <div class="field"><label for="fee">Delivery fee (tk) <span class="muted">— leave blank if not served</span></label>
    <input type="number" id="fee" name="fee" min="0" value="<?= $row && $row['fee'] === null ? '' : $v('fee') ?>"></div>
  <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:18px">
    <label style="display:flex;align-items:center;gap:7px;font-weight:600;color:#2B171F;margin:0">
      <input type="checkbox" name="cod_allowed" value="1" style="width:auto"
        <?= old('cod_allowed', $row['cod_allowed'] ?? 0) ? 'checked' : '' ?>> Cash on delivery allowed</label>
    <label style="display:flex;align-items:center;gap:7px;font-weight:600;color:#2B171F;margin:0">
      <input type="checkbox" name="is_limited" value="1" style="width:auto"
        <?= old('is_limited', $row['is_limited'] ?? 0) ? 'checked' : '' ?>> Limited service</label>
  </div>
  <div class="actions"><button class="btn" type="submit"><?= $isEdit ? 'Save changes' : 'Create zone' ?></button>
    <a class="btn ghost" href="<?= base_url('admin/zones') ?>">Cancel</a></div>
</form>
<?php $content = ob_get_clean(); include __DIR__ . '/../_layout.php';
