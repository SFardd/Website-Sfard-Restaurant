<?php
// ============================================================
// INCLUDES/CLASSES/ORDERMODEL.PHP
// CHILD CLASS — extends Database (INHERITANCE)
// ============================================================

class OrderModel extends Database
{
    // ---- READ: semua pesanan + nama akun pemilik (dipakai admin/orders.php) ----
    public function getAll()
    {
        return mysqli_query($this->conn, "SELECT o.*, u.username FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC");
    }

    // ---- READ: riwayat pesanan milik satu akun customer (dipakai riwayat_pesanan.php) ----
    public function getByUser(int $userId)
    {
        $stmt = mysqli_prepare($this->conn, "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    // ---- READ: detail item milik satu pesanan ----
    public function getItems(int $orderId)
    {
        $stmt = mysqli_prepare($this->conn, "SELECT nama_menu, qty FROM order_items WHERE order_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $orderId);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    // ---- CREATE: simpan pesanan baru, return id pesanan ----
    public function create(array $data): int
    {
        $stmt = mysqli_prepare($this->conn, "INSERT INTO orders
            (user_id, kode, nama_pemesan, telepon, jenis, alamat, catatan, ongkir, total, metode_pembayaran, bukti_transfer)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $userId = $data['user_id'] ?? null;
        mysqli_stmt_bind_param(
            $stmt, "issssssddss",
            $userId, $data['kode'], $data['nama_pemesan'], $data['telepon'], $data['jenis'],
            $data['alamat'], $data['catatan'], $data['ongkir'], $data['total'],
            $data['metode_pembayaran'], $data['bukti_transfer']
        );
        mysqli_stmt_execute($stmt);
        return mysqli_insert_id($this->conn);
    }

    // ---- CREATE: simpan satu item pesanan ----
    public function createItem(int $orderId, array $item): bool
    {
        $stmt = mysqli_prepare($this->conn, "INSERT INTO order_items
            (order_id, menu_item_id, nama_menu, harga, qty, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param(
            $stmt, "iisdid",
            $orderId, $item['id'], $item['nama'], $item['harga'], $item['qty'], $item['subtotal']
        );
        return mysqli_stmt_execute($stmt);
    }

    // ---- UPDATE: ubah status pesanan ----
    public function updateStatus(int $orderId, string $status): bool
    {
        $stmt = mysqli_prepare($this->conn, "UPDATE orders SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $orderId);
        return mysqli_stmt_execute($stmt);
    }

    // ---- COUNT (dipakai di dashboard admin) ----
    public function countAll(): int
    {
        $row = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) AS total FROM orders"));
        return (int) $row['total'];
    }
}
