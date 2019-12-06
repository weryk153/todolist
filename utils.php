<?php
  function getOne($conn, $id) {
    $sql = "SELECT * FROM todolist WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $arr = array('id' => $row['id'], 'content' => $row['content'], 'isCompleted' => $row['isCompleted']);
      // 轉成 json 並解決中文亂碼和美化排版
      $json = preg_replace_callback('/^ +/m', function ($m) {
        return str_repeat(' ', strlen ($m[0]) / 2);
      }, json_encode($arr, JSON_PRETTY_PRINT ^ JSON_UNESCAPED_UNICODE)); // 空格變兩個
      echo $json;  
    } else {
      $object = json_decode('{}');
      $object->none = '{}';
      echo $object->none;
    };
  }

  function getAll($conn) {
    $sql = "SELECT * FROM todolist";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
      $arr = [];
      while ($row = $result->fetch_assoc()) {
        $obj = (object) array('id' => $row['id'], 'content' => $row['content'], 'isCompleted' => $row['isCompleted']);
        array_push($arr, $obj);
        $json = preg_replace_callback('/^ +/m', function ($m) {
          return str_repeat(' ', strlen ($m[0]) / 2);
        }, json_encode($arr, JSON_PRETTY_PRINT ^ JSON_UNESCAPED_UNICODE)); // 空格變兩個
      }
      echo $json; 
    }
  }

  function post($conn, $content) {
    $sql = "INSERT INTO todolist(content, isCompleted) VALUE(?, false)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $content);
    if ($stmt->execute()) {
      $last_id = $stmt->insert_id;
      echo json_encode(array(
        'result' => '新增成功', 'id' => $last_id
      ));
    } else {
      echo json_encode(array(
        'result' => '失敗'
      ));  
    }
  }

  function patch($conn, $id, $content, $isCompleted) {
    $sql = "UPDATE todolist SET content = ?, isCompleted = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $content, $isCompleted, $id);
    if ($stmt->execute()) {
      echo json_encode(array(
        'result' => '修改成功'
      ));
    } else {
      echo json_encode(array(
        'result' => '失敗'
      ));
    }
  }

  function delete($conn, $id) {
    $sql = "DELETE FROM todolist WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
      echo json_encode(array(
        'result' => '刪除成功'
      ));
    } else {
      echo json_encode(array(
        'result' => '失敗'
      ));
    }
  }
?>