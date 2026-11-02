<?php
require_once 'classes/Room.php';
include 'header.php';

$in  = $_GET['check_in']  ?? date('Y-m-d');
$out = $_GET['check_out'] ?? date('Y-m-d', strtotime('+1 day'));
$g   = max(1, (int)($_GET['guests'] ?? 1));
$error = '';
$rooms = [];

if ($in >= $out)            $error = 'วันที่เช็คเอาต์ต้องอยู่หลังวันเช็คอิน';
elseif ($in < date('Y-m-d')) $error = 'ไม่สามารถเลือกวันที่ผ่านมาแล้ว';
else                         $rooms = (new Room())->findAvailable($in, $out, $g);
?>
<h1>ค้นหาห้องว่าง</h1>
<form method="get" class="search">
  <label>เช็คอิน <input type="date" name="check_in" value="<?= e($in) ?>" min="<?= date('Y-m-d') ?>" required></label>
  <label>เช็คเอาต์ <input type="date" name="check_out" value="<?= e($out) ?>" required></label>
  <label>ผู้เข้าพัก <input type="number" name="guests" value="<?= $g ?>" min="1" max="10"></label>
  <button>ค้นหาห้อง</button>
</form>

<?php if ($error): ?><p class="err"><?= e($error) ?></p><?php endif; ?>
<?php if (!$error && !$rooms): ?><p>ไม่มีห้องว่างในช่วงวันที่นี้ ลองเปลี่ยนวันที่ดูนะครับ</p><?php endif; ?>

<div class="grid">
<?php foreach ($rooms as $r): ?>
  <article class="room">
    <h2><?= e($r['name']) ?></h2>
    <p class="meta"><?= e($r['type']) ?> · รองรับ <?= (int)$r['capacity'] ?> ท่าน</p>
    <p><?= e($r['description']) ?></p>
    <p class="price"><?= number_format($r['price']) ?> บาท/คืน</p>
    <a class="btn" href="book.php?room_id=<?= (int)$r['id'] ?>&check_in=<?= e($in) ?>&check_out=<?= e($out) ?>&guests=<?= $g ?>">จองห้องนี้</a>
  </article>
<?php endforeach; ?>
</div>
<?php include 'footer.php'; ?>
