<?php
require_once __DIR__ . '/../public/config/db.php';

class ResourceController {
    public function index($table) {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM $table ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function store($table, $data) {
        global $pdo;
        if ($table === 'products') {
            $stmt = $pdo->prepare("INSERT INTO products (name, scent_notes, price, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data['name'], $data['scent_notes'], $data['price'], $data['description']]);
        }
        header("Location: index.php");
        exit;
    }

    public function destroy($table, $id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: index.php");
        exit;
    }
}