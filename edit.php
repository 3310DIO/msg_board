<?php
require 'db.php';
session_start();
$id = $_POST['msg_id'] ?? ''; //  從 GET 請求中獲取參數的值。如果該參數不存在，則將其設置為空字串。
if($id){
    $sql = "SELECT id, user_account, title, content, is_del FROM msg WHERE id = ? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["$id"]); // 執行了 SQL 查詢，並將 ID 綁定到查詢中的參數。
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    if($_SESSION["user_account"]!=$task["user_account"]){
    	header("Location: index.php");
    }
}

if(!$task){ //檢查 $task 變數是否為空。如果為空則將輸出 留言不存在，並重新導向到 index.php 頁面。
    echo "留言不存在";
    header("refresh:3;url=index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css123/style.css">
    <title>編輯留言</title>

</head>
<body>
    <h2 class="text_center">編輯留言</h2>
    <?php if(!$task['is_del']){ /* 判斷該留言是否被刪除 */ ?>
    <!-- <a onclick="history.back()" >
        <button type="submit">返回上一頁</button><br> 返回上一頁
    </a> -->
        <div class="wrap msg_box_bord">
            <span><a class="button_use" href="index.php" >返回首頁</a></span> <?php // 返回首頁 ?>
            <span><a class="button_use" href="content_reply.php?id=<?php echo htmlspecialchars($task['id']) ?>" >返回上一頁</a></span>
        </div>
        <div class="wrap msg_box msg_box_bord text_center">
            <form class="msg" action="config_msg.php?method=update" method="post">
                <input type="hidden" id="msg_id" name="msg_id" value="<?php echo htmlspecialchars($task['id']) ?>"/>
                <!-- <p>名字:<br><input type="text" id="msg_name" name="msg_name" value="<?php //echo htmlspecialchars($task['msg_name']) ?>" required/></p> -->
                <p>標題:<br><input type="text" id="title" name="title" value="<?php echo htmlspecialchars($task['title']) ?>" required/></p> <?php // value=顯示原本的內容 ?>
                <p>留言:<br>
                    <textarea class="textarea_style" id="content" name="content" rows="15" cols="80" required><?php echo htmlspecialchars($task['content']) ?></textarea>
                </p>
                <p><button class="button_use" type="submit">更新</button></p>
            </form>
            <form class="msg" id="form_del" action="config_msg.php?method=del" method="post"> <?php // 刪除留言 ?>
                <p>
                    <input type="hidden" name="btn_delete_id" value="<?php echo $task['id'] ?>"/>
                    <button class="button_use" type="button" id="btn_delete" onclick="del()" >刪除</button>
                </p>
            </form>
        </div>
    <?php }else{ // 若留言已刪除則跳出編輯
        echo "留言已刪除";
        header("refresh:1.5;url=index.php");
        exit;
    }?>
        

</body>
<script>
    function del(){ // 若刪除則送出表單
        if(!confirm('要刪除嗎？')){
            return false;
        }else{
            document.getElementById("form_del").submit();
        }
    }
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

