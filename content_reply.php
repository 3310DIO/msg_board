<?php
require 'db.php';
session_start();
$id = $_GET['id'] ?? ''; //  從 GET 請求中獲取參數的值。如果該參數不存在，則將其設置為空字串。
if($id){
    $sql = "SELECT msg.id, msg.user_account, msg.title, msg.content, msg.is_del, msg.created_at, msg.update_at, member.user_name FROM msg INNER JOIN member ON msg.user_account = member.user_account WHERE id = ? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["$id"]); // 執行了 SQL 查詢，並將 ID 綁定到查詢中的參數。
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    // if($_SESSION["user_account"]!=$task["user_account"]){
    // 	header("Location: index.php");
    // }
    $sql = "SELECT msg_reply.id, msg_reply.user_account, msg_reply.reply_time, msg_reply.user_reply, msg_reply.update_time, msg_reply.is_del, member.user_name FROM msg_reply INNER JOIN member ON msg_reply.user_account = member.user_account WHERE msg_reply.msg_id = ? ORDER BY msg_reply.reply_time ASC ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["$id"]); // 執行了 SQL 查詢，並將 ID 綁定到查詢中的參數。
    // $msg_reply = $stmt->fetch(PDO::FETCH_ASSOC);
    $floor_id = 0;
}

if(!$task){ //檢查 $task 變數是否為空。如果為空則將輸出 留言不存在，並重新導向到 index.php 頁面。
    echo "留言不存在";
    header("refresh:1;url=index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css123/style.css">
    <title>留言</title>
</head>
<body>
    <div class="top cnetered">
        <span style="text-align: right; width: 100%;"> <?php // 將登錄訊息顯示在最上層 ?>
            <?php if(isset($_SESSION["user_account"])){?>
                <table class="text_right" style="width: 460px;"> <?php // 有登錄則顯示登錄資訊 ?>
                    <tbody>
                        <td style="width:350px;">
                            <a class="button_use text_right" href="user_space.php?owner=<?php echo $_SESSION["user_account"];?>" id="btn_register" ><?php echo htmlspecialchars($_SESSION["user_name"]);?>，您好</a>
                        </td>
                        <td style="width:70px;">
                            <a class="button_use text_right" href="config_member.php?method=logout">登出</a>
                        </td>
                    </tbody>
                </table>
            <?php }else{?>
                <table class="text_right" style="width: 150px;"> <?php // 未登錄則顯示弄按鈕 ?>
                    <tbody>
                        <td style="width:70px;">
                            <a class="button_use text_right" href="login.php">登錄</a>
                        </td>
                        <td style="width:70px;">
                            <a class="button_use text_right" href="register.php">註冊</a>
                        </td>
                    </tbody>
                </table>
            <?php }?>
        </span>
    </div>
    <h2 class="text_center">
        <?php if(!$task['is_del']){ /* 判斷該留言是否被刪除 */ ?>
            <?php echo htmlspecialchars($task['title']) ?>
        <?php }else{ ?>
            留言已刪除 <?php // value=顯示原本的內容 ?>
        <?php }?>
    </h2>
    <div class="msg">
        <p><a class="button_use" href="index.php" >返回首頁</a></p> <?php // 返回首頁 ?>
        
        <table class="msg_box">
            <tbody>
                <td class="msg_floor msg_info">
                    <p>作者<p>
                    <a class="account_font" href="user_space.php?owner=<?php echo $task["user_account"];?>">
                        <p><?php echo htmlspecialchars($task["user_name"])?></p>
                    </a>
                </td>
                <td class="msg_content_reply msg_info">
                    <form style="word-break: break-all;" action="edit.php?id=<?php echo htmlspecialchars($task['id']) ?>" method="post">
                    <?php if(!$task['is_del']){ /* 判斷該留言是否被刪除 */ ?>
                        <input type="hidden" id="msg_id" name="msg_id" value="<?php echo htmlspecialchars($task['id']) ?>"/>
                        <!-- <p>名字:<br><input type="text" id="msg_name" name="msg_name" value="<?php //echo htmlspecialchars($task['msg_name']) ?>" required/></p> -->
                        
                        <pre style="white-space: pre-wrap;"><?php echo htmlspecialchars($task['content']) ?></pre>
                        <?php if (isset($_SESSION["user_account"]) && isset($task["user_account"]) && $_SESSION["user_account"] == $task["user_account"]) {?>
                            <button class="button_use text_right">修改</button> <?php // 僅有發布者能顯示 ?>
                        <?php }?>
                    <?php }else{ ?>
                        <p>留言已刪除</p> <?php // 留言被刪除 ?>
                    <?php }?>
                    </form>
                <td>
                <td class="msg_date msg_info">
                    <p>建立日期：<?php echo $task["created_at"];?></p>
                    <p>修改日期：<?php echo $task["update_at"];?></p>
                </td>
            </tbody>
        </table>
        <div class="wrap">
            <?php while($msg_reply = $stmt->fetch(PDO::FETCH_ASSOC)){ // PDO::FETCH_ASSOC 依照結果集中傳回的直欄名稱，傳回已編製索引的陣列。?>
                <div class="msg_box">
                    <table>
                        <tbody>
                            <tr>
                                <td class="msg_floor msg_info">
                                    <div class="check_box">
                                        <span><?php echo ++$floor_id; ?>樓</span> <?php // 僅有發布者能顯示修改 ?>
                                        <?php if (isset($_SESSION["user_account"]) && isset($msg_reply["user_account"]) && $_SESSION["user_account"] == $msg_reply["user_account"] && !($task["is_del"]) && !($msg_reply["is_del"])) {?>
                                            <input type="checkbox" id="check_box<?php echo $floor_id ?>" value="<?php echo $floor_id ?>">
                                            <label for="check_box<?php echo $floor_id ?>">修改</label>
                                        <?php }?>
                                    </div>
                                    <?php if(!($msg_reply["is_del"])){ ?>
                                        <a class="account_font" href="user_space.php?owner=<?php echo $msg_reply["user_account"];?>">
                                            <p><?php echo $msg_reply["user_name"];?></p>
                                        </a>
                                    <?php } ?>
                                </td>
                                <td class="content_reply msg_info" style="word-break: break-all;"> <?php //若一個單字過長，超過畫面的寬度，則將單字切斷，並換行。  ?>
                                    <?php if(!($msg_reply["is_del"])){ ?>
                                        <pre type="text" id="text_reply<?php echo $floor_id ?>" style="white-space: pre-wrap;"><?php echo $msg_reply["user_reply"];?></pre> <?php // 顯示回復  ?>
                                        <?php if (isset($_SESSION["user_account"]) && isset($msg_reply["user_account"]) && $_SESSION["user_account"] == $msg_reply["user_account"]) {?> <?php // 僅有回覆者能顯示  ?>
                                            <form style="width: 250px;" id="form_del<?php echo $floor_id ?>" action="config_msg.php?method=reply_del" method="post"> <?php // 刪除留言 ?>
                                                <input type="hidden" name="msg_id" value="<?php echo $task['id'] ?>"/>
                                                <input type="hidden" name="btn_delete_id" value="<?php echo $msg_reply['id'] ?>"/>
                                            </form>
                                            <form style="width: 250px;" id="form_sub<?php echo $floor_id ?>" action="config_msg.php?method=reply_update" method="post"> <?php // 更新留言 ?>
                                                <input type="hidden" name="msg_id" value="<?php echo $task['id'] ?>"/>
                                                <input type="hidden" name="reply_id" value="<?php echo $msg_reply['id'] ?>"/>
                                                <?php $line_count = substr_count($msg_reply["user_reply"], "\n");
                                                if($line_count < 2){
                                                    $line_count = 2;
                                                } ?>
                                                <textarea id="text_reply_modify<?php echo $floor_id ?>" name="reply" rows="<?php echo $line_count ?>" cols="50" hidden><?php echo $msg_reply["user_reply"];?></textarea>
                                            </form>
                                                <button class="button_use" type="button" id="btn_delete<?php echo $floor_id ?>" onclick="del(<?php echo $floor_id ?>)" hidden>刪除</button>
                                                <button class="button_use" type="button" id="btn_reply_update<?php echo $floor_id ?>" onclick="sub(<?php echo $floor_id ?>)" hidden>更新</button>
                                        <?php } 
                                    }else{
                                        echo '回復已刪除';
                                    } ?>
                                </td>
                                <td class="msg_date msg_info">
                                    <?php if(!($msg_reply["is_del"])){ ?>
                                        <p>回復日期：<?php echo $msg_reply["reply_time"];?></p>
                                        <p>修改日期：<?php echo $msg_reply["update_time"];?></p>
                                    <?php }else{ ?>
                                        <p>刪除日期：<?php echo $msg_reply["update_time"];?></p>
                                    <?php } ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            <?php } ?>
        </div>
        <?php if(!$task['is_del']){ /* 判斷該留言是否被刪除，若刪除則無法回復 */ ?>
            <form class="reply_box" action="config_msg.php?method=reply" method="post">
                <input type="hidden" id="msg_id" name="msg_id" value="<?php echo htmlspecialchars($task['id']) ?>"/>
                <p><button class="button_use" type="submit">回復</button></p>
                <textarea type="text" id="reply" name="reply" rows="10" cols="80" required></textarea>
            </from>
        <?php }?>
    </div>

</body>
<script>
    var checkboxes = document.querySelectorAll("input[type='checkbox']");

    // 為每個checkbox添加事件監聽器
    checkboxes.forEach(function(checkbox){
        checkbox.addEventListener("click", function(){
            var id = this.value;
            const check_box = document.getElementById("check_box"+id);
            const text_reply_modify = document.getElementById("text_reply_modify"+id);
            const btn_reply_update = document.getElementById("btn_reply_update"+id);
            const btn_delete = document.getElementById("btn_delete"+id);
            const text_reply = document.getElementById("text_reply"+id);
            check_box.addEventListener("change", function(){ // 修改目標的狀態，若有勾選checkbox則顯示出修改內容
                if(this.checked){
                    text_reply_modify.removeAttribute('hidden');
                    btn_reply_update.removeAttribute('hidden');
                    btn_delete.removeAttribute('hidden');
                    text_reply.setAttribute('hidden', true);
                }else{
                    text_reply_modify.setAttribute('hidden', true);
                    btn_reply_update.setAttribute('hidden', true);
                    btn_delete.setAttribute('hidden', true);
                    text_reply.removeAttribute('hidden');
                }
            });
            
        });
    });
    function sub(id){ // 送出修改表單
        document.getElementById("form_sub"+id).submit();
    }
    function del(id){ // 送出刪除表單
        if(!confirm('要刪除嗎？')){
            return false;
        }else{
            document.getElementById("form_del"+id).submit();
        }
    }
</script>
</html>

