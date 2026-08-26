<?php
$isEdit = $row !== null;
$v = static fn(string $k, $d = '') => esc(old($k, $row[$k] ?? $d) ?? '', 'attr');
$action = $isEdit ? base_url('admin/products/' . $row['id']) : base_url('admin/products');
ob_start();
?>
<form method="post" action="<?= $action ?>" class="card" style="padding:18px;max-width:820px">
  <?= csrf_field() ?>
  <div class="row">
    <div class="field"><label for="name">Name</label>
      <input type="text" id="name" name="name" required value="<?= $v('name') ?>"></div>
    <div class="field"><label for="slug">Slug <span class="muted">(blank = auto)</span></label>
      <input type="text" id="slug" name="slug" value="<?= $v('slug') ?>"></div>
  </div>
  <div class="row">
    <div class="field"><label for="category_id">Category</label>
      <select id="category_id" name="category_id" required>
        <?php $sel = (int) old('category_id', $row['category_id'] ?? 0); ?>
        <option value="">Choose…</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $sel === (int) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div class="field"><label for="price">Price (tk)</label>
      <input type="number" id="price" name="price" min="0" required value="<?= $v('price', 0) ?>"></div>
    <div class="field"><label for="min_qty">Minimum order qty</label>
      <input type="number" id="min_qty" name="min_qty" min="1" value="<?= $v('min_qty', 1) ?>"></div>
  </div>
  <div class="row">
    <div class="field"><label for="note">Note <span class="muted">(e.g. ~650 gm)</span></label>
      <input type="text" id="note" name="note" value="<?= $v('note') ?>"></div>
    <div class="field"><label for="kcal">kcal / 100g</label>
      <input type="number" id="kcal" name="kcal" min="0" value="<?= $v('kcal') ?>"></div>
  </div>
  <div class="field"><label for="image">Image URL</label>
    <input type="text" id="image" name="image" value="<?= $v('image') ?>"></div>
  <div class="field"><label for="ingredients">Key ingredients</label>
    <textarea id="ingredients" name="ingredients" rows="3"><?= esc(old('ingredients', $row['ingredients'] ?? '') ?? '') ?></textarea></div>

  <?php
  $flags = ['is_active' => 'Visible on the shop', 'is_new' => 'Show “New” badge',
            'is_featured' => 'Feature on the home page', 'in_bagel_pool' => 'Counts toward the pooled bagel minimum'];
  ?>
  <div style="display:flex;flex-wrap:wrap;gap:16px;margin:6px 0 18px">
    <?php foreach ($flags as $k => $lbl):
      $on = (bool) old($k, $row[$k] ?? ($k === 'is_active' ? 1 : 0)); ?>
      <label style="display:flex;align-items:center;gap:7px;font-weight:600;color:#2B171F;margin:0">
        <input type="checkbox" name="<?= $k ?>" value="1" <?= $on ? 'checked' : '' ?> style="width:auto"> <?= $lbl ?>
      </label>
    <?php endforeach; ?>
  </div>

  <div class="actions">
    <button class="btn" type="submit"><?= $isEdit ? 'Save changes' : 'Create product' ?></button>
    <a class="btn ghost" href="<?= base_url('admin/products') ?>">Cancel</a>
  </div>
</form>
<?php $content = ob_get_clean(); include __DIR__ . '/../_layout.php';
