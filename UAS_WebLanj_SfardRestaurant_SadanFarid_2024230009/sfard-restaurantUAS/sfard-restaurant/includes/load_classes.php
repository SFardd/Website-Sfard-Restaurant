<?php
// ============================================================
// INCLUDES/LOAD_CLASSES.PHP
// Loader — require semua class (Database + Model turunannya)
// sekali panggil, dipakai di halaman yang butuh OOP.
// Pakai __DIR__ supaya path selalu benar walau dipanggil
// dari root ("login.php") maupun dari admin/ ("admin/menu_list.php").
// ============================================================

require_once __DIR__ . '/classes/Database.php';        // PARENT CLASS
require_once __DIR__ . '/classes/MenuModel.php';        // CHILD: extends Database
require_once __DIR__ . '/classes/ReservationModel.php'; // CHILD: extends Database
require_once __DIR__ . '/classes/MessageModel.php';     // CHILD: extends Database
require_once __DIR__ . '/classes/OrderModel.php';       // CHILD: extends Database
require_once __DIR__ . '/classes/UserModel.php';        // CHILD: extends Database
