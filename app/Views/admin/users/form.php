<?php
$isEdit  = $row !== null;
$v       = static fn (string $k, $d = '') => esc(old($k, $row[$k] ?? $d) ?? '', 'attr');
$action  = $isEdit ? base_url('admin/users/' . $row['id']) : base_url('admin/users');
$curRole = old('role', $row['role'] ?? 'staff');
$isMe    = $isEdit && (int) $row['id'] === ($meId ?? 0);
$isLast  = $isEdit && $row['role'] === 'admin' && $row['is_active'] && ($adminCount ?? 2) <= 1;
$locked  = $isMe || $isLast;   // role/status cannot be changed in these cases
ob_start();
?>
<div style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));align-items:start;max-width:900px">

  <form method="post" action="<?= $action ?>" class="card" style="padding:18px">
    <?= csrf_field() ?>
    <h2 style="margin:0 0 14px;font-size:15px">Account</h2>

    <div class="field"><label for="name">Full name</label>
      <input type="text" id="name" name="name" required value="<?= $v('name') ?>"></div>

    <div class="field"><label for="email">Email</label>
      <input type="email" id="email" name="email" required autocomplete="off" value="<?= $v('email') ?>"></div>

    <div class="field"><label for="role">Role</label>
      <select id="role" name="role" <?= $locked ? 'disabled' : '' ?>>
        <?php foreach ($roles as $r): ?>
          <option value="<?= esc($r) ?>" <?= $curRole === $r ? 'selected' : '' ?>><?= esc($r) ?></option>
        <?php endforeach; ?>
      </select>
      <?php if ($locked): ?>
        <input type="hidden" name="role" value="<?= esc($curRole, 'attr') ?>">
        <div class="muted" style="font-size:12px;margin-top:5px">
          <?= $isMe ? 'You cannot change your own role.' : 'This is the only active administrator.' ?>
        </div>
      <?php endif; ?>
    </div>

    <label style="display:flex;align-items:center;gap:7px;font-weight:600;color:#2B171F;margin:0 0 16px">
      <input type="checkbox" name="is_active" value="1" style="width:auto"
        <?= old('is_active', $row['is_active'] ?? 1) ? 'checked' : '' ?>
        <?= $locked ? 'disabled' : '' ?>> Active (can sign in)
    </label>
    <?php if ($locked): ?><input type="hidden" name="is_active" value="1"><?php endif; ?>

    <?php if (!$isEdit): ?>
      <div class="field"><label for="password">Password <span class="muted">(min <?= (int) $minPw ?> characters)</span></label>
        <input type="password" id="password" name="password" required autocomplete="new-password"></div>
      <div class="field"><label for="password_confirm">Confirm password</label>
        <input type="password" id="password_confirm" name="password_confirm" required autocomplete="new-password"></div>
    <?php endif; ?>

    <div class="actions">
      <button class="btn" type="submit"><?= $isEdit ? 'Save changes' : 'Create user' ?></button>
      <a class="btn ghost" href="<?= base_url('admin/users') ?>">Cancel</a>
    </div>
  </form>

  <?php if ($isEdit): ?>
    <form method="post" action="<?= base_url('admin/users/' . $row['id'] . '/password') ?>" class="card" style="padding:18px">
      <?= csrf_field() ?>
      <h2 style="margin:0 0 6px;font-size:15px">Set a new password</h2>
      <p class="muted" style="margin:0 0 14px;font-size:12.5px">
        Replaces <strong><?= esc($row['name']) ?></strong>'s password immediately.
        The current one is not needed.
      </p>

      <div class="field"><label for="np">New password <span class="muted">(min <?= (int) $minPw ?> characters)</span></label>
        <input type="password" id="np" name="password" required autocomplete="new-password"></div>
      <div class="field"><label for="np2">Confirm new password</label>
        <input type="password" id="np2" name="password_confirm" required autocomplete="new-password"></div>

      <button class="btn" type="submit">Update password</button>
    </form>
  <?php endif; ?>

</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
