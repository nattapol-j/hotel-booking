<?php
require_once __DIR__ . '/Database.php';

class Room
{
    /** ห้องที่ว่างในช่วงวันที่ที่เลือก และรองรับจำนวนผู้เข้าพัก */
    public function findAvailable(string $in, string $out, int $guests = 1): array
    {
        $sql = "SELECT r.* FROM rooms r
                WHERE r.is_active = 1 AND r.capacity >= :g
                AND NOT EXISTS (
                    SELECT 1 FROM bookings b
                    WHERE b.room_id = r.id AND b.status <> 'cancelled'
                      AND b.check_in < :out AND b.check_out > :in
                )
                ORDER BY r.price";
        $st = Database::get()->prepare($sql);
        $st->execute([':g' => $guests, ':in' => $in, ':out' => $out]);
        return $st->fetchAll();
    }

    public function find(int $id): ?array
    {
        $st = Database::get()->prepare("SELECT * FROM rooms WHERE id = ? AND is_active = 1");
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }
}
