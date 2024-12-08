<?php
include '../entity/db_connection.php';

class User
{
    private $conn;

    // Constructor handles database connection internally
    public function __construct()
    {
        $this->conn = $this->connectDB();
    }

    // Function to connect to the database
    private function connectDB()
    {
        include '../entity/db_connection.php';
        return isset($conn) ? $conn : null;
    }

    public function getUserByUsernameOrEmail($identifier)
    {
        // Join users with roles to get role name based on role_id
        $query = "SELECT u.username, u.email, u.password, u.status, r.role_name AS role 
                  FROM users u 
                  JOIN roles r ON u.role_id = r.id 
                  WHERE u.username = ? OR u.email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function getUserIdByUsername($username)
    {
        $sql = "SELECT user_id FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['user_id'];
        } else {
            return null; // User not found
        }
    }
}
