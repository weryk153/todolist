<?php

  require_once('conn.php');
  require_once('utils.php');
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept');
  header("Access-Control-Allow-Methods: GET,POST,PATCH,DELETE");
  header("Access-Control-Allow-Credentials: true");

  Class todoAPI {
    public function __construct($conn) {
      $this->conn = $conn;
    }

    public function getMethod($requestMethod) {      
      switch ($requestMethod) {
        case 'GET':
          if (isset($_GET['id'])) {
            getOne($this->conn, $_GET['id']);
          } else {
            getAll($this->conn);
          }
          break;
        case 'POST':
          if (isset($_POST['content']) && !empty($_POST['content'])) {
            post($this->conn, $_POST['content']);
          }
          break;
        case 'PATCH':
          parse_str(file_get_contents('php://input'), $_PATCH); // 先取得 PATCH 的值再轉成變數
          if ($_PATCH['content'] || $_PATCH['isCompleted']) {
            patch($this->conn, $_GET['id'], $_PATCH['content'], $_PATCH['isCompleted']);
          }
          break;
        case 'DELETE':
          if (isset($_GET['id'])) {
            delete($this->conn, $_GET['id']);
          }
          break;
      }
    }
  }
  $todo = new todoAPI($conn);
  $todo->getMethod($_SERVER['REQUEST_METHOD']);
?>
