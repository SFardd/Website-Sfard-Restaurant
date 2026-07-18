<?php
// ============================================================
// INCLUDES/CLASSES/MENUMODEL.PHP
// CHILD CLASS — extends Database (INHERITANCE)
// Mewarisi $conn, constructor, dan destructor dari Database,
// tanpa perlu nulis ulang kode koneksinya.
// ============================================================

class MenuModel extends Database
{
    // ---- READ: semua menu (dengan opsi search keyword) ----
    public function getAll(string $keyword = '')
    {
        if ($keyword !== '') {
            $stmt = mysqli_prepare($this->conn, "SELECT m.*, c.name AS category_name FROM menu_items m
                JOIN categories c ON m.category_id = c.id
                WHERE m.name LIKE ? ORDER BY m.id DESC");
            $like = "%" . $keyword . "%";
            mysqli_stmt_bind_param($stmt, "s", $like);
            mysqli_stmt_execute($stmt);
            return mysqli_stmt_get_result($stmt);
        }
        return mysqli_query($this->conn, "SELECT m.*, c.name AS category_name FROM menu_items m
            JOIN categories c ON m.category_id = c.id ORDER BY m.id DESC");
    }

    // ---- READ: semua menu (termasuk yang habis) untuk halaman menu.php ----
    public function getShopMenu()
    {
        return mysqli_query($this->conn, "SELECT m.*, c.slug AS kategori_slug FROM menu_items m
            JOIN categories c ON m.category_id = c.id ORDER BY m.id ASC");
    }

    // ---- READ: semua menu yang tersedia saja (dipakai di menu.php / pesan.php) ----
    public function getAvailable()
    {
        return mysqli_query($this->conn, "SELECT m.*, c.slug AS kategori_slug, c.name AS kategori_nama
            FROM menu_items m JOIN categories c ON m.category_id = c.id
            WHERE m.is_available = 1 ORDER BY m.id ASC");
    }

    // ---- READ: semua kategori ----
    public function getCategories()
    {
        return mysqli_query($this->conn, "SELECT * FROM categories ORDER BY id ASC");
    }

    // ---- READ: satu menu berdasarkan id ----
    public function find(int $id)
    {
        $stmt = mysqli_prepare($this->conn, "SELECT * FROM menu_items WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    // ---- CREATE ----
    public function create(array $data): bool
    {
        $stmt = mysqli_prepare($this->conn, "INSERT INTO menu_items
            (category_id, name, description, price, image, badge, rating, is_spicy, is_veg, is_available)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param(
            $stmt, "issdssdiii",
            $data['category_id'], $data['name'], $data['description'], $data['price'],
            $data['image'], $data['badge'], $data['rating'],
            $data['is_spicy'], $data['is_veg'], $data['is_available']
        );
        return mysqli_stmt_execute($stmt);
    }

    // ---- UPDATE ----
    public function update(int $id, array $data): bool
    {
        $stmt = mysqli_prepare($this->conn, "UPDATE menu_items SET
            category_id=?, name=?, description=?, price=?, image=?, badge=?, rating=?, is_spicy=?, is_veg=?, is_available=?
            WHERE id=?");
        mysqli_stmt_bind_param(
            $stmt, "issdssdiiii",
            $data['category_id'], $data['name'], $data['description'], $data['price'],
            $data['image'], $data['badge'], $data['rating'],
            $data['is_spicy'], $data['is_veg'], $data['is_available'], $id
        );
        return mysqli_stmt_execute($stmt);
    }

    // ---- DELETE ----
    public function delete(int $id): bool
    {
        $stmt = mysqli_prepare($this->conn, "DELETE FROM menu_items WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

    // ---- COUNT (dipakai di dashboard admin) ----
    public function countAll(): int
    {
        $row = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) AS total FROM menu_items"));
        return (int) $row['total'];
    }
}
