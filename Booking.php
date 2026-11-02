<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Room.php';

class Booking
{
    public function create(array $d): int
    {
        $db = Database::get();
        $db->beginTransaction();
        try {
            // ล็อกแถวห้องเพื่อกันจองซ้อนกัน
            $st = $db->prepare("SELECT * FROM rooms WHERE id = ? AND is_active = 1 FOR UPDATE");
            $st->execute([$d['room_id']]);
            $room = $st->fetch();
            if (!$room) throw new RuntimeException('ไม่พบห้องนี้');
            if ($d['guests'] > $room['capacity']) throw new RuntimeException('จำนวนผู้เข้าพักเกินที่ห้องรองรับ');

            $st = $db->prepare("SELECT COUNT(*) FROM bookings
                WHERE room_id = ? AND status <> 'cancelled' AND check_in < ? AND check_out > ?");
            $st->execute([$d['room_id'], $d['check_out'], $d['check_in']]);
            if ($st->fetchColumn() > 0) throw new RuntimeException('ห้องนี้ไม่ว่างในช่วงวันที่เลือก');

            $nights = (new DateTime($d['check_in']))->diff(new DateTime($d['check_out']))->days;
            $total = $nights * $room['price'];

            $st = $db->prepare("INSERT INTO bookings
                (room_id, guest_name, guest_phone, guest_email, check_in, check_out, guests, total_price)
                VALUES (?,?,?,?,?,?,?,?)");
            $st->execute([$d['room_id'], $d['guest_name'], $d['guest_phone'], $d['guest_email'],
                          $d['check_in'], $d['check_out'], $d['guests'], $total]);
            $id = (int)$db->lastInsertId();
            $db->commit();
            return $id;
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function all(): array
    {
        return Database::get()->query("SELECT b.*, r.name AS room_name FROM bookings b
            JOIN rooms r ON r.id = b.room_id ORDER BY b.created_at DESC")->fetchAll();
    }

    public function setStatus(int $id, string $status): void
    {
        if (!in_array($status, ['pending', 'confirmed', 'cancelled'], true)) return;
        Database::get()->prepare("UPDATE bookings SET status = ? WHERE id = ?")->execute([$status, $id]);
    }
}
