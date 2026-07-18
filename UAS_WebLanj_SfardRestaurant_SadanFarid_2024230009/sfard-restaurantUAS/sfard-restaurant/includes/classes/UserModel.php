<?php
// ============================================================
// INCLUDES/CLASSES/USERMODEL.PHP
// CHILD CLASS — extends Database (INHERITANCE)
// ============================================================

class UserModel extends Database
{
    // ---- READ: cari user berdasarkan username (dipakai login.php) ----
    public function findByUsername(string $username)
    {
        $stmt = mysqli_prepare($this->conn, "SELECT * FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    // ---- READ: cek apakah username sudah dipakai ----
    public function usernameExists(string $username): bool
    {
        $stmt = mysqli_prepare($this->conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        return mysqli_stmt_num_rows($stmt) > 0;
    }

    // ---- CREATE: daftar akun baru (dipakai register.php, role selalu customer) ----
    public function create(string $username, string $hashedPassword, string $email): bool
    {
        $stmt = mysqli_prepare($this->conn, "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'customer')");
        mysqli_stmt_bind_param($stmt, "sss", $username, $hashedPassword, $email);
        return mysqli_stmt_execute($stmt);
    }

    // ---- COUNT (dipakai di dashboard admin) ----
    public function countAll(): int
    {
        $row = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) AS total FROM users"));
        return (int) $row['total'];
    }
}
