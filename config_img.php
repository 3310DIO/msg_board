<?php
	require 'db.php';
    session_start();
	switch($_GET["method"]){ // 根據得到的method種類選擇使用的function
		case "upload":
			upload($pdo);
			break;
        case "img_del":
            img_del($pdo);
            break;
		default:
			break;
	}
    function upload($pdo){ // 上傳檔案使用
        // global $pdo;
        if(!($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["user_account"]) && isset($_POST['submit_img']) && isset($_FILES['my_img']))){
            echo "<script>";
            echo "alert('上傳失敗');";
            echo "window.history.go(-1);";
            echo "</script>";
        }else{
            // echo '<pre>';
            // print_r($_FILES['my_img']);
            // echo '<pre>';
            $user_account = $_SESSION["user_account"];
            $img_name = $_FILES['my_img']['name'];
            $img_size = $_FILES['my_img']['size'];
            $img_tmp_name = $_FILES['my_img']['tmp_name'];
            $img_error = $_FILES['my_img']['error'];
            $img_w_h = getimagesize($_FILES['my_img']['tmp_name']);
            $width = $img_w_h[0]; // 獲得寬度
            $height = $img_w_h[1]; // 獲得高度
            if($width > $height){ // 判斷高還是寬比較大
                $w_h = 1;
            }else{
                $w_h = 0;
            }

            if(!($img_error === 0)){ // 判斷是否有錯誤訊息
                echo "<script>";
                echo "alert('未知的錯誤');";
                echo "window.history.go(-1);";
                echo "</script>";
            }else{
                if($img_size > 10485760){ // 判斷大小是否超過10MB
                    echo "<script>";
                    echo "alert('上傳影像不能超過10MB');";
                    echo "window.history.go(-1);";
                    echo "</script>";
                }else{
                    $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                    // echo"$img_ex";
                    $img_ex_lc = strtolower($img_ex);

                    $allow_img = array("jpg", "jpeg", "png"); // 允許的圖片格式
                    if(!(in_array($img_ex_lc, $allow_img))){ // 判斷格式是否符合預設條件
                        echo "<script>";
                        echo "alert('不支援的影像格式');";
                        echo "window.history.go(-1);";
                        echo "</script>";
                    }else{
                        $new_img_name = uniqid("IMG-", true).'.'.$img_ex_lc;
                        $img_upload_path = 'upload/img/'.$new_img_name;
                        move_uploaded_file($img_tmp_name, $img_upload_path); // 將上傳檔案複製進指定資料夾

                        $sql = "INSERT INTO img_upload(user_account, img_url, width_height) VALUES (?, ?, ?)"; // VALUES (?,?,?) 使用?代表輸入值。
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([$user_account, $new_img_name, $w_h]); // array($msg_name, $title, $content) 將獲取的值輸入進?中。
                        echo "<script>";
                        echo "alert('上傳成功');";
                        echo "location.href='user_space.php?owner=$user_account';";
                        echo "</script>";
                    }
                }
            }
        }
    }
    function img_del($pdo){ // 刪除圖片使用
        // global $pdo;
        if(!($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['btn_delete_id']))){ // 檢查是否接收到了 POST 請求並且請求中的 "id" 參數不為空。
            echo '非法操作';
            header('refresh:1;url=index.php');
            exit;
        }else{
            $user_account = $_SESSION["user_account"];
            $id = $_POST['btn_delete_id'];
            $sql = "SELECT img_url FROM img_upload WHERE id = ? AND user_account = ? ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["$id", $user_account]);
            $task = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$task){ //檢查 $task 變數是否為空。如果為空則代表沒有該圖片或非圖片傭有者進行的修改
                echo "圖片不存在";
                header("refresh:3;url=index.php");
                exit;
            }else{
                $new_img_name = $task['img_url'];
                $img_upload_path = 'upload/img/'.$new_img_name;
                $img_del_path = 'upload/del_img/'.$new_img_name;
                rename($img_upload_path, $img_del_path); // 將刪除的圖片移動至待刪除的資料夾

                // $sql = "DELETE FROM msg WHERE id = :id";
                $sql = "UPDATE img_upload SET is_del = 1 WHERE id = ? ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id]);
            
                echo "<script>";
                echo "alert('已刪除');";
                echo "location.href='user_space.php?owner=$user_account';";
                echo "</script>";
            }
        }
    }
?>