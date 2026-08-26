<?php
$isEdit = $row !== null;
$v      = static fn (string $k, $d = '') => esc(old($k, $row[$k] ?? $d) ?? '', 'attr');
$action = $isEdit ? base_url('admin/testimonials/' . $row['id']) : base_url('admin/testimonials');
$stars  = (int) old('stars', $row['stars'] ?? 5);
ob_start();
?>
<form method="post" action="<?= $action ?>" class="card" style="padding:18px;max-width:660px">
  <?= csrf_field() ?>

  <div class="row">
    <div class="field"><label for="name">Customer name</label>
      <input type="text" id="name" name="name" required value="<?= $v('name') ?>"></div>
    <div class="field"><label for="stars">Rating</label>
      <select id="stars" name="stars">
        <?php for ($i = 5; $i >= 1; $i--): ?>
          <option value="<?= $i ?>" <?= $stars === $i ? 'selected' : '' ?>><?= str_repeat('★', $i) ?> (<?= $i ?>)</option>
        <?php endfor; ?>
      </select></div>
  </div>

  <div class="field"><label for="quote">Quote</label>
    <textarea id="quote" name="quote" rows="4" required><?= esc(old('quote', $row['quote'] ?? '') ?? '') ?></textarea></div>

  <div class="field"><label for="item">Item mentioned <span class="muted">(optional)</span></label>
    <input type="text" id="item" name="item" value="<?= $v('item') ?>"></div>

  <label style="display:flex;align-items:center;gap:7px;font-weight:600;color:#2B171F;margin:0 0 18px">
    <input type="checkbox" name="is_published" value="1" style="width:auto"
      <?= old('is_published', $row['is_published'] ?? 1) ? 'checked' : '' ?>> Show on the website
  </label>

  <div class="actions">
    <button class="btn" type="submit"><?= $isEdit ? 'Save changes' : 'Create testimonial' ?></button>
    <a class="btn ghost" href="<?= base_url('admin/testimonials') ?>">Cancel</a>
  </div>
</form>
<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
