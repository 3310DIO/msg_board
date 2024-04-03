<?php
require 'db.php';
session_start();
$user_account = $_SESSION['user_account'] ?? ''; //  從 GET 請求中獲取參數的值。如果該參數不存在，則將其設置為空字串。
$this_space_user = $_GET['owner'];
if($this_space_user){
    $sql = "SELECT user_account, user_name, user_password FROM member WHERE user_account = ? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["$this_space_user"]); // 執行了 SQL 查詢，並將 ID 綁定到查詢中的參數。
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    // if($_SESSION["user_account"]!=$task["user_account"]){
    // 	header("Location: index.php");
    // }
    $sql = "SELECT id, img_url, width_height FROM img_upload WHERE user_account = ? AND is_del = ? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["$this_space_user", "0"]); // 執行了 SQL 查詢，並將 ID 綁定到查詢中的參數。
    // $img_load = $stmt->fetch(PDO::FETCH_ASSOC);
    $img_id = 0;
    
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
    <title><?php echo htmlspecialchars($task["user_account"])?>的個人空間</title>
    <style>
        /* .img_size img{
            width: 100%;
            height: auto;
        } */
        /* body {
            flex-wrap: wrap;
        }
        li {
            text-align: -webkit-match-parent;
        } */
        /* .img_box{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 200px));
            grid-gap: 10px;
        } */
    </style>

</head>
<body>
    <div class="top cnetered">
        <span style="text-align: right; width: 100%;">
            <?php if(isset($_SESSION["user_account"])){?>
                <table class="text_right" style="width: 460px;">
                    <tbody>
                        <?php if($user_account != $this_space_user){ ?>
                            <td style="width:350px;">
                                <a class="button_use text_right" href="user_space.php?owner=<?php echo $_SESSION["user_account"];?>" id="btn_register" ><?php echo htmlspecialchars($_SESSION["user_name"]);?>，您好</a>
                            </td>
                        <?php } ?>
                        <td style="width:70px;">
                            <a class="button_use text_right" href="config_member.php?method=logout">登出</a>
                        </td>
                    </tbody>
                </table>
            <?php }else{?>
                <table class="text_right" style="width: 150px;">
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
    <h1 class="text_center"><?php echo htmlspecialchars($task["user_account"])?>的空間</h1>
    <div class="msg">
        <a class="button_use" href="index.php" >返回首頁</a>
        <?php if($user_account == $this_space_user){ ?>
            <a class="button_use" href="setting_account.php" >帳號設定</a>
        <?php } ?>
        <div class="wrap msg_box_bord img_box_bord">
            <div class="msg_box account_box text_center">
                <div class="img_box">
                    <?php while($img_load = $stmt->fetch(PDO::FETCH_ASSOC)){ $img_id++;
                        if($img_load["width_height"]){
                            $img_w_h = "width: 190px;";
                        }else{
                            $img_w_h = "height: 190px;";
                        } ?>
                        <div class="img_size">
                            <?php if($user_account == $this_space_user){ ?>
                                <form class="msg" id="form_del_<?php echo $img_id ?>" action="config_img.php?method=img_del" method="post"> <?php // 刪除留言 ?>
                                    <input type="hidden" name="btn_delete_id" value="<?php echo $img_load['id'] ?>"/>
                                    <button class="button_use x_button clickable-box_x" type="button" id="btn_delete" onclick="del(<?php echo $img_id ?>)" >&times;</button>
                                </form>
                            <?php } ?>
                            <img class="img_img_size" id="img_id_<?php echo $img_id ?>" style="<?php echo $img_w_h ?>;" src="upload/img/<?php echo htmlspecialchars($img_load["img_url"]); ?>">
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php if($user_account == $this_space_user){ ?>
                <form class="text_center" action="config_img.php?method=upload" method="post" enctype="multipart/form-data">
                    <input type="file" name="my_img">
                    <input type="submit" name="submit_img" value="上傳">
                </form>
            <?php } ?>
        </div>
        
    </div>

</body>
<script>
    // 確認刪除圖片
    function del(id){
        if(!confirm('要刪除圖片嗎？')){
            return false;
        }else{
            document.getElementById("form_del_"+id).submit();
        }
    }
</script>
</html>