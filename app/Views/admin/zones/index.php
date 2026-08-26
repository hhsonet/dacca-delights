<?php ob_start(); ?>
<div class="card">
  <div class="hd"><h2><?= count($rows) ?> delivery zones</h2>
    <a class="btn" href="<?= base_url('admin/zones/create') ?>">+ New zone</a></div>
  <div class="tablewrap"><table>
    <thead><tr><th>Area</th><th>Fee</th><th>Cash on delivery</th><th>Limited</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $z): ?>
      <tr>
        <td><strong><?= esc($z['name']) ?></strong></td>
        <td><?= $z['fee'] === null
              ? '<span class="pill" style="background:#FDECEA;color:#B3261E">Not served</span>'
              : number_format((int) $z['fee']) . ' tk' ?></td>
        <td><?= $z['cod_allowed'] ? '<span class="pill" style="background:#E8F5EE;color:#17693F">Allowed</span>' : '<span class="muted">Online only</span>' ?></td>
        <td><?= $z['is_limited'] ? 'Yes' : '<span class="muted">—</span>' ?></td>
        <td class="actions">
          <a class="btn ghost sm" href="<?= base_url('admin/zones/' . $z['id'] . '/edit') ?>">Edit</a>
          <form method="post" action="<?= base_url('admin/zones/' . $z['id'] . '/delete') ?>"
                onsubmit="return confirm('Delete this zone?')">
            <?= csrf_field() ?><button class="btn danger sm" type="submit">Delete</button></form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody></table></div>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../_layout.php';
