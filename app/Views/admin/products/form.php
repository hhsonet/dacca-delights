<?php
$isEdit = $row !== null;
$v = static fn(string $k, $d = '') => esc(old($k, $row[$k] ?? $d) ?? '', 'attr');
$action = $isEdit ? base_url('admin/products/' . $row['id']) : base_url('admin/products');
ob_start();
?>
<form method="post" action="<?= $action ?>" class="card" style="padding:18px;max-width:820px">
  <?= csrf_field() ?>
  <?php if ($isEdit): ?>
    <div class="field">
      <label>Product code</label>
      <div style="font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:19px;font-weight:700;
                  letter-spacing:.12em;color:#561530;background:#FFF9F1;border:1px solid #EADFE2;
                  border-radius:10px;padding:10px 14px;display:inline-block"><?= esc($row['code']) ?></div>
      <div class="muted" style="font-size:12px;margin-top:5px">Assigned by the system and fixed — photos are filed under this code.</div>
    </div>
  <?php endif; ?>

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

<?php if ($isEdit):
  $count = count($photos);
  $full  = $count >= $maxPhotos;
?>
<div class="card" style="padding:18px;max-width:820px;margin-top:16px">
  <h2 style="margin:0 0 4px;font-size:15px">Photos</h2>
  <p class="muted" style="margin:0 0 16px;font-size:12.5px">
    <?= $count ?> of <?= (int) $maxPhotos ?> used. Stored in
    <code>uploads/products/<?= esc($row['code']) ?>/</code>.
    <?php if ($full): ?>
      <strong>Folder is full — the next upload replaces the last photo.</strong>
    <?php endif; ?>
  </p>

  <?php if ($photos): ?>
    <div style="display:grid;gap:12px;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));margin-bottom:16px">
      <?php foreach ($photos as $ix => $ph): ?>
        <?php $isAi = !empty($ph['is_ai']); ?>
        <div style="border:1px solid <?= $full && $ix === $count - 1 ? '#F5AD18' : '#EADFE2' ?>;
                    border-radius:12px;overflow:hidden;background:#fff">
          <div style="aspect-ratio:1/1;background:#F3E7D6;position:relative">
            <img src="<?= esc(base_url('uploads/products/' . $row['code'] . '/' . $ph['filename'])) ?>"
                 alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block">
            <span title="<?= $isAi ? 'AI-generated image' : 'Real photograph' ?>"
                  style="position:absolute;left:7px;top:7px;display:inline-flex;align-items:center;gap:4px;
                         padding:3px 8px;border-radius:999px;font-size:10.5px;font-weight:700;
                         background:<?= $isAi ? 'rgba(158,28,96,.92)' : 'rgba(23,105,63,.92)' ?>;color:#fff">
              <?= $isAi ? '✦ AI' : '◉ Real' ?>
            </span>
          </div>
          <div style="padding:9px;display:flex;flex-direction:column;gap:7px">
            <span class="muted" style="font-size:11.5px">
              <?= $ix === 0 ? 'Primary' : '#' . ($ix + 1) ?>
              <?php if ($full && $ix === $count - 1): ?> · <span style="color:#8A5A08;font-weight:700">next replaces</span><?php endif; ?>
            </span>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
              <form method="post" action="<?= base_url('admin/products/' . $row['id'] . '/photos/' . $ph['id'] . '/origin') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="is_ai" value="<?= $isAi ? '0' : '1' ?>">
                <button class="btn ghost sm" type="submit"
                        title="<?= $isAi ? 'Mark as a real photograph' : 'Mark as AI-generated' ?>">
                  Mark <?= $isAi ? 'Real' : 'AI' ?>
                </button>
              </form>
              <form method="post" action="<?= base_url('admin/products/' . $row['id'] . '/photos/' . $ph['id'] . '/delete') ?>"
                    onsubmit="return confirm('Remove this photo? The file is deleted from the server.')">
                <?= csrf_field() ?><button class="btn danger sm" type="submit">Remove</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="muted" style="margin:0 0 16px">No photos yet.</p>
  <?php endif; ?>

  <form method="post" action="<?= base_url('admin/products/' . $row['id'] . '/photos') ?>"
        enctype="multipart/form-data" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
    <?= csrf_field() ?>
    <div class="field" style="margin:0;flex:1;min-width:220px">
      <label for="photo">Add a photo <span class="muted">JPG, PNG, WebP or GIF · max 5 MB</span></label>
      <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp,image/gif" required>
    </div>
    <button class="btn" type="submit"><?= $full ? 'Upload &amp; replace last' : 'Upload' ?></button>
  </form>
</div>
<?php endif; ?>
<?php $content = ob_get_clean(); include __DIR__ . '/../_layout.php';
