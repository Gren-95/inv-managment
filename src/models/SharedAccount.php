<?php

class SharedAccount {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function create($data) {
        $sql = "INSERT INTO shared_accounts (username, email, passcode) VALUES (:username, :email, :passcode)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'passcode' => $data['passcode']
        ]);
    }

    public function getAll() {
        $sql = "SELECT * FROM shared_accounts ORDER BY username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get($id) {
        $sql = "SELECT * FROM shared_accounts WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $sql = "UPDATE shared_accounts SET username = :username, email = :email, passcode = :passcode WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'username' => $data['username'],
            'email' => $data['email'],
            'passcode' => $data['passcode']
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM shared_accounts WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
} 