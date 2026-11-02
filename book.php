<?php
require_once 'classes/Room.php';
require_once 'classes/Booking.php';

$roomObj = new Room();
$src = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
$room = $roomObj->find((int)($src['room_id'] ?? 0));
if (!$room) { http_response_code(404); exit('ไม่พบห้อง'); }

$in = $src['check_in'] ?? ''; $out = $src['check_out'] ?? ''; $g = max(1, (int)($src['guests'] ?? 1));
$error = ''; $doneId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['guest_name'] ?? ''); $phone = trim($_POST['guest_phone'] ?? ''); $email = trim($_POST['guest_email'] ?? '');
    if ($name === '' || !preg_match('/^[0-9\-+ ]{9,15}$/', $phone) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'กรุณากรอกชื่อ เบอร์โทร และอีเมลให้ถูกต้อง';
    } elseif ($in >= $out || $in < date('Y-m-d')) {
        $error = 'ช่วงวันที่ไม่ถูกต้อง';
    } else {
        try {
            $doneId = (new Booking())->create([
                'room_id' => $room['id'], 'guest_name' => $name, 'guest_phone' => $phone,
                'guest_email' => $email, 'check_in' => $in, 'check_out' => $out, 'guests' => $g,
            ]);
        } catch (RuntimeException $ex) { $error = $ex->getMessage(); }
    }
}
include 'header.php';
?>
<?php if ($doneId): ?>
  <h1>จองสำเร็จ</h1>
  <p>เลขที่การจอง <strong>#<?= (int)$doneId ?></strong> สถานะ: รอยืนยัน ทางโรงแรมจะติดต่อกลับทางโทรศัพท์</p>
  <a class="btn" href="index.php">กลับหน้าแรก</a>
<?php else: ?>
  <h1>จอง <?= e($room['name']) ?></h1>
  <p><?= e($in) ?> ถึง <?= e($out) ?> · <?= $g ?> ท่าน · <?= number_format($room['price']) ?> บาท/คืน</p>
  <?php if ($error): ?><p class="err"><?= e($error) ?></p><?php endif; ?>
  <form method="post" class="stack">
    <input type="hidden" name="room_id" value="<?= (int)$room['id'] ?>">
    <input type="hidden" name="check_in" value="<?= e($in) ?>">
    <input type="hidden" name="check_out" value="<?= e($out) ?>">
    <input type="hidden" name="guests" value="<?= $g ?>">
    <label>ชื่อ-นามสกุล <input name="guest_name" required value="<?= e($_POST['guest_name'] ?? '') ?>"></label>
    <label>เบอร์โทร <input name="guest_phone" required value="<?= e($_POST['guest_phone'] ?? '') ?>"></label>
    <label>อีเมล <input type="email" name="guest_email" required value="<?= e($_POST['guest_email'] ?? '') ?>"></label>
    <button>ยืนยันการจอง</button>
  </form>
<?php endif; include 'footer.php'; ?>
