<?php
	require 'db.php';
    session_start();
	switch($_GET["method"]){ // 根據得到的method種類選擇使用的function
		case "add":
			add($pdo);
			break;
		case "update":
			update($pdo);
			break;
		case "del":
			del($pdo);
			break;
        case "reply":
            reply($pdo);
            break;
        case "reply_update":
            reply_update($pdo);
            break;
        case "reply_del":
            reply_del($pdo);
            break;
		default:
			break;
	}
    function add($pdo){ // 新增留言使用
        // global $pdo;
        if(!($_SERVER["REQUEST_METHOD"] == "POST")){
            echo '非法操作';
            header('refresh:1;url=index.php');
            exit;
        }else{
            $user_account = $_SESSION["user_account"];
            $title = $_POST['title'];
            $content = $_POST['content']; 
            if($title=='' || $content==''){ // 判斷是否有輸入值輸入，沒有則返回首頁
                echo '非法操作';
                header('refresh:1;url=index.php');
                exit;
            }else{
                if(mb_strlen($title) > 256 || mb_strlen($content) > 5000){ // 判斷輸入值是否超過上限
                    echo "<script>";
                    echo "alert('輸入字元超過上限');";
                    echo "location.href='new_msg.php';";
                    echo "</script>";
                }else{
                    $sql = "INSERT INTO msg(user_account, title, content) VALUES (?, ?, ?)"; // VALUES (?,?,?) 使用?代表輸入值。
                    $stmt = $pdo->prepare($sql);
            
                    $stmt->execute([$user_account, $title, $content]); // array($msg_name, $title, $content) 將獲取的值輸入進?中。
            
                    header('refresh:0;url=index.php');
                    exit;
                }
            }
        }
    }
    function update($pdo){ // 更新留言使用
        // global $pdo;
        if(!($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['msg_id']))){
            echo '非法操作';
            header('refresh:1;url=index.php');
            exit;
        }else{
            $id = $_POST['msg_id'];
            $user_account = $_SESSION["user_account"];
            $title = $_POST['title'];
            $content = $_POST['content'];
            if($title=='' || $content==''){ // 判斷是否有輸入值輸入，沒有則返回首頁
                echo '非法操作';
                header('refresh:1;url=index.php');
                exit;
            }else{
                if(mb_strlen($title) > 256 || mb_strlen($content) > 5000){ // 判斷輸入值是否超過上限
                    echo "<script>";
                    echo "alert('輸入標題超過256個字或內容超過5000上限');";
                    echo "window.history.back();";
                    echo "</script>";
                }else{
                    $sql = "SELECT user_account FROM msg WHERE id = ? ";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(["$id"]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if($user_account!=$row['user_account']){ // 判斷是否是發佈者進行的修改
                        echo '非法操作';
                        header('refresh:2;url=index.php');
                        exit;
                    }else{
                        $sql = "UPDATE msg SET title = ? , content = ? WHERE id = ? ";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(["$title", "$content", "$id"]);
                        header("refresh:0;url=content_reply.php?id=$id");
                        exit;
                    }
                }
            }
        }
    }
    function del($pdo){ // 刪除留言使用
        // global $pdo;
        if(!($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['btn_delete_id']))){ // 檢查是否接收到了 POST 請求並且請求中的 "id" 參數不為空。
            echo '非法操作';
            header('refresh:1;url=index.php');
            exit;
        }else{
            $id = $_POST['btn_delete_id'];
            $user_account = $_SESSION["user_account"];
            $sql = "SELECT user_account FROM msg WHERE id = ? ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["$id"]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($user_account!=$row['user_account']){ // 判斷是否是發佈者進行的修改
                echo '非法操作';
                header('refresh:2;url=index.php');
                exit;
            }else{
                $id = $_POST['btn_delete_id'];
                // $sql = "DELETE FROM msg WHERE id = :id";
                $sql = "UPDATE msg SET is_del = 1 WHERE id = ? ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id]);
            
                header('refresh:0;url=index.php');
                exit;
            }
        }
    }
    function reply($pdo){ // 新增回復使用
        // global $pdo;
        if(!($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['msg_id']))){
            echo '非法操作';
            header('refresh:1;url=index.php');
            exit;
        }else{
            $id = $_POST['msg_id'];
            $user_account = $_SESSION["user_account"] ?? '';
            $reply = $_POST['reply'];
            if($reply==''){ // 判斷是否有輸入值輸入，沒有則返回首頁
                echo '非法操作';
                header('refresh:1;url=index.php');
                exit;
            }else{
                if(mb_strlen($reply) > 1500){ // 判斷輸入值是否超過上限
                    echo "<script>";
                    echo "alert('輸入字元超過1500上限');";
                    echo "window.history.back();";
                    echo "</script>";
                }else{
                    if($user_account == ''){ // 判斷是否有登錄
                        echo "<script>";
                        echo "alert('請登錄再留言');";
                        echo "location.href='login.php';";
                        echo "</script>";
                    }else{
                        $user_account = $_SESSION["user_account"];
                        $sql = "INSERT INTO msg_reply(msg_id, user_account, user_reply) VALUES (?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(["$id", "$user_account", "$reply"]);
                        header("refresh:0;url=content_reply.php?id=$id");
                        exit;
                    }
                }
            }
        }
    }
    function reply_update($pdo){ // 更新回復使用
        // global $pdo;
        if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['msg_id'])){
            $id = $_POST['msg_id'];
            $reply_id = $_POST['reply_id'];
            $user_account = $_SESSION["user_account"];
            $reply = $_POST['reply'];
            if($reply==''){ // 判斷是否有輸入值輸入，沒有則返回首頁
                echo '請輸入文字';
                header("refresh:1;url=content_reply.php?id=$id");
                exit;
            }else{
                if(mb_strlen($reply) > 1500){ // 判斷輸入值是否超過上限
                    echo "<script>";
                    echo "alert('輸入字元超過上限');";
                    echo "window.history.back();";
                    echo "</script>";
                }else{
                    $sql = "SELECT user_account FROM msg_reply WHERE id = ? ";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(["$reply_id"]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if($user_account != $row['user_account']){ // 判斷是否為發布者進行的修改
                        echo '非法操作';
                        header("refresh:2;url=content_reply.php?id=$id");
                        exit;
                    }else{
                        $sql = "UPDATE msg_reply SET user_reply = ? WHERE id = ? ";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(["$reply", "$reply_id"]);
                        header("refresh:0;url=content_reply.php?id=$id");
                        exit;
                    }
                }
            }
        }
    }
    function reply_del($pdo){ // 刪除回復使用
        // global $pdo;
        if(!($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['btn_delete_id']))){ // 檢查是否接收到了 POST 請求並且請求中的 "id" 參數不為空。
            header("refresh:0;url=index.php");
            exit;
        }else{
            $btn_delete_id = $_POST['btn_delete_id'];
            $user_account = $_SESSION["user_account"];
            $sql = "SELECT user_account FROM msg_reply WHERE id = ? ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["$btn_delete_id"]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($user_account!=$row['user_account']){ // 判斷是否是發佈者進行的修改
                echo '非法操作';
                header('refresh:2;url=index.php');
                exit;
            }else{
                $msg_id = $_POST['msg_id'];
                // $sql = "DELETE FROM msg WHERE id = :id";
                $sql = "UPDATE msg_reply SET is_del = 1 WHERE id = ? ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$btn_delete_id]);
            
                header("refresh:0;url=content_reply.php?id=$msg_id");
                exit;
            }
        }
    }
?>