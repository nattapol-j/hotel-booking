<?php
session_start();
require_once 'classes/Booking.php';
$cfg = require 'config.php';
$err = '';

if (isset($_POST['password'])) {
    if (hash_equals($cfg['admin_password'], $_POST['password'])) { session_regenerate_id(true); $_SESSION['admin'] = true; }
    else $err = 'รหัสผ่านไม่ถูกต้อง';
}
if (isset($_GET['logout'])) { session_destroy(); header('Location: admin.php'); exit; }

$b = new Booking();
if (!empty($_SESSION['admin']) && isset($_POST['id'], $_POST['status'])) {
    $b->setStatus((int)$_POST['id'], $_POST['status']);
    header('Location: admin.php'); exit;
}
include 'header.php';

if (empty($_SESSION['admin'])): ?>
  <h1>เข้าสู่ระบบเจ้าหน้าที่</h1>
  <?php if ($err): ?><p class="err"><?= e($err) ?></p><?php endif; ?>
  <form method="post" class="stack"><label>รหัสผ่าน <input type="password" name="password" required></label><button>เข้าสู่ระบบ</button></form>
<?php else: ?>
  <h1>รายการจองทั้งหมด</h1><p><a href="?logout=1">ออกจากระบบ</a></p>
  <div class="scroll"><table>
    <tr><th>#</th><th>ห้อง</th><th>ผู้จอง</th><th>โทร</th><th>เข้าพัก</th><th>ยอดรวม</th><th>สถานะ</th></tr>
    <?php foreach ($b->all() as $r): ?>
    <tr>
      <td><?= (int)$r['id'] ?></td><td><?= e($r['room_name']) ?></td><td><?= e($r['guest_name']) ?></td>
      <td><?= e($r['guest_phone']) ?></td><td><?= e($r['check_in']) ?> → <?= e($r['check_out']) ?></td>
      <td><?= number_format($r['total_price']) ?></td>
      <td><form method="post" class="inline"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
        <select name="status" onchange="this.form.submit()">
          <?php foreach (['pending' => 'รอยืนยัน', 'confirmed' => 'ยืนยันแล้ว', 'cancelled' => 'ยกเลิก'] as $k => $v): ?>
            <option value="<?= $k ?>" <?= $r['status'] === $k ? 'selected' : '' ?>><?= $v ?></option>
          <?php endforeach; ?>
        </select></form></td>
    </tr>
    <?php endforeach; ?>
  </table></div>
<?php endif; include 'footer.php'; ?>
