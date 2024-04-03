<?php
    include_once "db.php";
    session_start();
    if(!isset($_SESSION["user_name"])){
    	header("refresh:1;url=login.php");
    }

?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css123/style.css">
    <title>新增留言</title>
</head>
<body>
    <h2 class="text_center">新增留言</h2>
    <div class="wrap msg_box_bord">
        <p><a class="button_use" href="index.php">返回首頁</a></p>
    </div>
    <div class="wrap msg_box msg_box_bord text_center">
        <form class="msg" action="config_msg.php?method=add" method="post">
            <!-- <p><a class="button_use" href="index.php">返回首頁</a></p> -->
            <!-- <p>名字：<br><input type="text" name="msg_name" required></p> -->
            <p>標題：<br><input type="text" name="title" required></p> <!-- required表示該項目為必填 -->
            <p>內容：<br>
                <textarea class="textarea_style" id="content" name="content" rows="15" cols="80" required></textarea>
            </p>
            <p><button class="button_use" type="submit">送出</button></p>
        </form>
    </div>
</body>
<script>
    // 判斷輸入內若超過文字框則加大文字框範圍
    const textarea_size = document.getElementById('content');
    const max_characters = 70;
    textarea_size.addEventListener('input', function(){
        const characters_rows = (this.value.length)/max_characters;
        const rows = this.value.split('\n').length;
        const rows_sum = rows+characters_rows;
        if(rows_sum > 15){
            this.rows = rows_sum;
        }else{
            this.rows = 15;
        }
    });
</script>
</html>
