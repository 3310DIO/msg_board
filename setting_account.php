<?php
require 'db.php';
session_start();
$user_account = $_SESSION['user_account'] ?? ''; //  從 GET 請求中獲取參數的值。如果該參數不存在，則將其設置為空字串。
if($user_account){
    $sql = "SELECT user_account, user_name, user_password FROM member WHERE user_account = ? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["$user_account"]); // 執行了 SQL 查詢，並將 ID 綁定到查詢中的參數。
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    // if($_SESSION["user_account"]!=$task["user_account"]){
    // 	header("Location: index.php");
    // }
}

if(!$task){ //檢查 $task 變數是否為空。如果為空則將輸出 留言不存在，並重新導向到 index.php 頁面。
    echo "非法操作";
    header("refresh:1;url=index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css123/style.css">
    <title>編輯帳號</title>

</head>
<body>
    <h1 class="text_center">編輯<?php echo htmlspecialchars($task["user_account"])?>的帳號</h1>
    <div class="wrap msg_box_bord">
        <a class="button_use" href="index.php" >返回首頁</a>
        <a class="button_use" href="user_space.php?owner=<?php echo $user_account ?>" >返回個人空間</a>
    </div>
    <div class="wrap msg_box_bord">
        <div class="msg_box account_box text_center">
            <form class="register_input" id="form_register" action="config_member.php?method=set_account" method="post">
                <input type="hidden" name="user_account" value="<?php echo htmlspecialchars($task["user_account"])?>">
                <p>暱稱：<br><input type="text" name="user_name" value="<?php echo htmlspecialchars($task["user_name"])?>" placeholder="輸入暱稱"></p>
                <p>舊密碼：<br><input type="password" id="user_password_old" name="user_password_old" placeholder="輸入舊密碼"></p>
                <p>新密碼：<br><input type="password" id="user_password_new" name="user_password_new" placeholder="輸入新密碼">
                    <br><span class="small_font">*密碼需在8~25字間，且密碼須包含英文大小寫、數字及特殊符號</span>
                </p>
                <p>確認：<br><input type="password" id="user_password_confirm" placeholder="再次輸入密碼"></p>
                <div class="check_box">
                    <input type="checkbox" id="check_box">
                    <label for="check_box">顯示密碼</label>
                </div><br>
                <button class="button_use" type="button" id="btn_delete" onclick="check_password()" >送出</button>
            </form>
            <br>
        </div>
    </div>
</body>
<script>
    // 判斷密碼及確認密碼是否相同
    function check_password(){
        var pw = document.getElementById("user_password_new").value;
        var pwc = document.getElementById("user_password_confirm").value;
        if(pw != pwc){
            alert('密碼不同');
        }else{
            document.getElementById("form_register").submit();
        }
    }
    // 顯示密碼
    const check_box = document.getElementById("check_box");
    const user_password_old = document.getElementById("user_password_old");
    const user_password_new = document.getElementById("user_password_new");
    const user_password_confirm = document.getElementById("user_password_confirm");
    check_box.addEventListener("change", function(){
        if(this.checked){
            user_password_old.type = "text";
            user_password_new.type = "text";
            user_password_confirm.type = "text";
        }else{
            user_password_old.type = "password";
            user_password_new.type = "password";
            user_password_confirm.type = "password";
        }
    });
</script>
</html>