<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css123/style.css">
    <title>註冊</title>
    
</head>
<body>
    <h1 class="text_center">註冊</h1>
    <div class="wrap text_center setting_box">
        <div class="msg_box msg_box_bord text_center">
            <form class="text_center" id="form_register" action="config_member.php?method=register" method="post">
                <p>帳號：<input type="text" name="user_account" placeholder="帳號不可再更改" required></p>
                <p>暱稱：<input type="text" name="user_name" placeholder="輸入暱稱" required></p>
                <p>密碼：<input type="password" id="user_password" name="user_password" placeholder="輸入密碼" required>
                    <br><span class="small_font">*密碼需在8~25字間，且密碼須包含英文大小寫、數字及特殊符號</span>
                </p>
                <p>確認：<input type="password" id="user_password_confirm" placeholder="再次輸入密碼" required></p>
                <div class="check_box">
                    <input type="checkbox" id="check_box">
                    <label for="check_box">顯示密碼</label>
                </div>
                <p>
                    <a class="button_use" href="login.php">去登錄</a>
                    <button class="button_use" type="button" id="btn_register" onclick="check_password()">註冊</button></br>
                </p>
            </form>
            <a class="button_use" href="index.php" >返回首頁</a>
        </div>
    </div>

</body>
<script>
    // 判斷密碼及確認密碼是否相同
    function check_password(){
        var pw = document.getElementById("user_password").value;
        var pwc = document.getElementById("user_password_confirm").value;
        if(pw != pwc){
            alert('密碼不同');
        }else{
            document.getElementById("form_register").submit();
        }
    }
    // 顯示密碼
    const check_box = document.getElementById("check_box");
    const user_password = document.getElementById("user_password");
    const user_password_confirm = document.getElementById("user_password_confirm");
    check_box.addEventListener("change", function(){
        if(this.checked){
            user_password.type = "text";
            user_password_confirm.type = "text";
        }else{
            user_password.type = "password";
            user_password_confirm.type = "password";
        }
    });
</script>
</html>