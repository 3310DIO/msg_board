<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css123/style.css">
    <title>登錄</title>

</head>
<body>
    <h1 class="text_center">登錄</h1>
    <div class="wrap text_center setting_box">
        <div class="msg_box msg_box_bord text_center">
            <form class="text_center" action="config_member.php?method=login" method="post">
                <p>帳號：<input type="text" name="user_account" placeholder="輸入帳號" required></p>
                <p>密碼：<input type="password" name="user_password" id="password_input" placeholder="輸入密碼" required></p>
                <div class="check_box">
                    <input type="checkbox" id="check_box">
                    <label for="check_box">顯示密碼</label>
                </div>
                <p>
                    <button class="button_use" type="submit">登錄</button>
                    <a class="button_use" href="register.php">去註冊</a>
                </p>
            </form>
            <a class="button_use" href="index.php">返回首頁</a>
        </div>
    </div>

</body>
<script>
    // 顯示密碼
    const check_box = document.getElementById("check_box");
    const password_input = document.getElementById("password_input");
    check_box.addEventListener("change", function(){
        if(this.checked){
            password_input.type = "text";
        }else{
            password_input.type = "password";
        }
    });
</script>
</html>