<?php
// ============================================================
// INCLUDES/CLASSES/MESSAGEMODEL.PHP
// CHILD CLASS — extends Database (INHERITANCE)
// ============================================================

class MessageModel extends Database
{
    // ---- READ: semua pesan kontak (dipakai admin/messages.php) ----
    public function getAll()
    {
        return mysqli_query($this->conn, "SELECT * FROM messages ORDER BY created_at DESC");
    }

    // ---- CREATE: simpan pesan kontak baru (dipakai contact.php) ----
    public function create(array $data): bool
    {
        $stmt = mysqli_prepare($this->conn, "INSERT INTO messages (nama, email, topik, pesan, rating) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssi", $data['nama'], $data['email'], $data['topik'], $data['pesan'], $data['rating']);
        return mysqli_stmt_execute($stmt);
    }

    // ---- COUNT (dipakai di dashboard admin) ----
    public function countAll(): int
    {
        $row = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) AS total FROM messages"));
        return (int) $row['total'];
    }
}
