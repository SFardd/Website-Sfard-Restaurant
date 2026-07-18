<?php
// ============================================================
// INCLUDES/CLASSES/RESERVATIONMODEL.PHP
// CHILD CLASS — extends Database (INHERITANCE)
// ============================================================

class ReservationModel extends Database
{
    // ---- READ: semua reservasi + nama akun pemilik (dipakai admin/reservations.php) ----
    public function getAll()
    {
        return mysqli_query($this->conn, "SELECT r.*, u.username FROM reservations r
            LEFT JOIN users u ON r.user_id = u.id
            ORDER BY r.tanggal DESC, r.jam DESC");
    }

    // ---- READ: riwayat reservasi milik satu akun customer (dipakai riwayat_reservasi.php) ----
    public function getByUser(int $userId)
    {
        $stmt = mysqli_prepare($this->conn, "SELECT * FROM reservations WHERE user_id = ? ORDER BY tanggal DESC, jam DESC");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    // ---- CREATE: simpan reservasi baru (dipakai reservation.php) ----
    public function create(array $data): bool
    {
        $stmt = mysqli_prepare($this->conn, "INSERT INTO reservations
            (user_id, kode, nama, email, telepon, tanggal, jam, tamu, meja, paket, catatan, metode_pembayaran, bukti_transfer)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $userId = $data['user_id'] ?? null;
        mysqli_stmt_bind_param(
            $stmt, "issssssisssss",
            $userId, $data['kode'], $data['nama'], $data['email'], $data['telepon'],
            $data['tanggal'], $data['jam'], $data['tamu'], $data['meja'], $data['paket'], $data['catatan'],
            $data['metode_pembayaran'], $data['bukti_transfer']
        );
        return mysqli_stmt_execute($stmt);
    }

    // ---- COUNT (dipakai di dashboard admin) ----
    public function countAll(): int
    {
        $row = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) AS total FROM reservations"));
        return (int) $row['total'];
    }
}
