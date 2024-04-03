<?php
require 'db.php'; //讀取db.php並引入檔案
session_start();

$one_page_msg = 10;
$current_page = (isset($_GET['page']) ? $_GET['page'] : 1 );
$start_page = $one_page_msg * ($current_page-1);

if(isset($_GET['search'])){
    $search_term = $_GET['search'];
    // if($search_term == '%' || $search_term == ''){
    //     header("refresh:0;url=index.php");
    // }else{
        // 使用 $search_term 執行搜尋資料庫的查詢
        $sql = "SELECT msg.id, msg.user_account, msg.title, msg.content, msg.created_at, msg.update_at, msg.is_del, member.user_name FROM msg INNER JOIN member ON msg.user_account = member.user_account WHERE msg.title LIKE ? ORDER BY msg.update_at DESC LIMIT $start_page, $one_page_msg ";
        $stmt = $pdo->prepare($sql);

        $patterns = array();
        $patterns[0] = '/%/';
        $patterns[1] = '/_/';
        $replacements = array();
        $replacements[0] = '\%';
        $replacements[1] = '\_';
        $search_term_replace = preg_replace($patterns, $replacements, $search_term);

        $stmt->execute(["%$search_term_replace%"]);
        $total_count_query = "SELECT COUNT(*) AS total_count FROM msg WHERE title LIKE ?"; // COUNT(*) AS total_count
        $total_counts = $pdo->prepare($total_count_query);
        
        $total_counts->execute(["%$search_term_replace%"]);
        $total_result = $total_counts->fetch(PDO::FETCH_ASSOC);
        $total_count = $total_result['total_count'];
        $current_url = 'index.php?search=' . $search_term . '&';
    // }
}else{
    $search_term = '';
    // 如果沒有搜索，則顯示所有結果
    $sql = "SELECT msg.id, msg.user_account, msg.title, msg.content, msg.created_at, msg.update_at, msg.is_del, member.user_name FROM msg INNER JOIN member ON msg.user_account = member.user_account ORDER BY msg.id DESC LIMIT $start_page, $one_page_msg ";
    $stmt = $pdo->query($sql);
    $total_count_query = "SELECT COUNT(*) AS total_count FROM msg WHERE title LIKE ?"; // COUNT(*) AS total_count
    $total_counts = $pdo->prepare($total_count_query);
    
    $total_counts->execute(["%"]);
    $total_result = $total_counts->fetch(PDO::FETCH_ASSOC);
    $total_count = $total_result['total_count'];
    $current_url = 'index.php?';
}

$total_page = ceil($total_count / $one_page_msg);

?>


<!DOCTYPE html>
<html lang="zh-Hant-TW"> <?php //標註網頁的語系 ?>
<head>
    <meta charset="UTF-8"> <?php //指定網頁所使用的編碼 ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <?php //設定 viewport 屬性，使頁面在各種設備上呈現正確  ?>
    <link rel="stylesheet" href="css123/style.css">
    <title>留言板</title>
    <style>
        /* .msg_ed {
            float: right;
        } */
    </style>
</head>
<body>
    <div class="top cnetered">
        <span style="text-align: right; width: 100%;">
            <?php if(isset($_SESSION["user_account"])){?>
                <table class="text_right" style="width: 460px;">
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
    <h1 class="text_center">
        <?php if(isset($_GET['search']) && $search_term != ''){ ?>
            搜尋：<?php echo htmlspecialchars($search_term); ?><br>共<?php echo htmlspecialchars($total_count); ?>則留言
        <?php }else{ ?>
            留言板
        <?php } ?>
    </h1>
    <div class="wrap">
        <div class="msg_search">
            <form class="msg_search-bar" action="index.php"> <?php //form表單 ?>
                <label for="site-search">搜索標題：</label> <?php //for 這個屬性指定了與此標籤關聯的表單控件的 id。  ?>
                <input class="search-bar" type="text" id="site-search" name="search" placeholder="輸入標題" value="<?php echo htmlspecialchars($search_term); ?>"></label>
                <?php //type="text"：指定了輸入框的類型為文本輸入框，用戶可以輸入文字、placeholder="輸入標題"：設置了輸入框的佔位符文字為 "輸入標題"，當輸入框為空時顯示在輸入框中，用戶點擊後自動消失。 ?>
                <button class="button_use">Search</button> <?php //button產生按鈕，type預設為submit  ?>
            </form>
        </div>
        <div class="text_center">
            <p><?php echo htmlspecialchars($current_page); ?> / <?php echo htmlspecialchars($total_page); ?></p>
        </div>
        <div class="msg text_center">
            <?php if($current_page > 1){ ?>
                <span class="text_left">
                    <a class="button_use" href="<?php echo $current_url ?>page=<?php echo $current_page-1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </span>
            <?php } ?>
            <?php for($i = 1 ; $i <= $total_page ; $i++){
                if($i == $current_page){ ?>
                    <span class="current_color"><?php echo $i ?></span>
                <?php }elseif($i == 1){ ?>
                    <span class="page-item"><a class="button_use" href="<?php echo $current_url ?>page=<?php echo $i ?>"><?php echo $i ?></a></span>
                    <?php if($i < $current_page-3){ ?>
                        <span class="page-item">...</span>
                    <?php } ?>
                <?php }elseif($i == $total_page){ ?>
                    <?php if($i > $current_page+3){ ?>
                        <span class="page-item">...</span>
                    <?php } ?>
                    <span class="page-item"><a class="button_use" href="<?php echo $current_url ?>page=<?php echo $i ?>"><?php echo $i ?></a></span>
                <?php }elseif($i > $current_page-3 && $i < $current_page+3){ ?>
                    <span class="page-item"><a class="button_use" href="<?php echo $current_url ?>page=<?php echo $i ?>"><?php echo $i ?></a></span>
                <?php } ?>
            <?php } ?>
            <?php if($current_page < $total_page){ ?>
                <span class="text_right">
                    <a class="button_use" href="<?php echo $current_url ?>page=<?php echo $current_page+1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </span>
            <?php } ?>
        </div>
        <div class="msg new_msg">
            <?php if(isset($_SESSION["user_account"])){?>
                <a class="button_use" href="new_msg.php">新增留言</a> <?php //href 屬性指定了超連結的目標 URL  ?>
            <?php }?>
            <?php if($current_page != 1 || $search_term != ''){?>
                <a class="button_use" href="index.php">返回首頁</a>
            <?php }?>
        </div>
    </div>
    <div class="wrap">
        <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)){ // PDO::FETCH_ASSOC 依照結果集中傳回的直欄名稱，傳回已編製索引的陣列。
            if(!($row["is_del"])){?>
                <div class="msg msg_box">
                    <div class="msg msg_id">
                        <a href="user_space.php?owner=<?php echo $row["user_account"];?>">
                            <span class="msg_id_name msg_info text-container account_font">作者：<?php echo $row["user_name"];?></span>
                        </a>
                        <div class="msg_title msg_info text-container">
                            <span>標題：<?php echo $row["title"];?></span>
                            <?php // if (isset($_SESSION["user_account"]) && isset($row["user_account"]) && $_SESSION["user_account"] == $row["user_account"]) { ?>
                                <!-- <a class="button_use text_right" href="edit.php?id=<?php //echo $row['id']; ?>">修改</a> -->
                            <?php // } ?>
                        </div>
                    </div>
                    <table>
                        <tbody>
                            
                            <tr>
                                <td class="msg_floor msg_info">
                                    <span>樓：<?php echo $row["id"];?></span>
                                </td>
                                <td class="msg_content msg_info clickable-box" style="word-break: break-all;"> <?php //若一個單字過長，超過畫面的寬度，則將單字切斷，並換行。  ?>
                                    <a href="content_reply.php?id=<?php echo $row['id']; ?>">
                                        <?php if(mb_strlen($row["content"]) > 100){
                                            $truncated_text = mb_substr($row["content"], 0, 97, 'UTF-8') . '...';
                                        }else{
                                            $truncated_text = $row["content"]; // 如果文字長度未超過最大長度，則返回原始文字
                                        } ?>
                                        <pre class="msg_info text-container" style="white-space: pre-wrap;"><?php echo $truncated_text;?></pre> <?php //保留文本中的空格  ?>
                                    </a>
                                </td>
                                <td class="msg_date msg_info">
                                    <p>建立日期：<?php echo $row["created_at"];?></p>
                                    <p>修改日期：<?php echo $row["update_at"];?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php }elseif($row["is_del"]){ ?>
                <div class="msg msg_box">
                    <table>
                        <tbody>
                            <tr>
                                <td class="msg_floor msg_info">
                                    <span>樓：<?php echo $row["id"];?></span>
                                </td>
                                <td class="msg_del msg_info clickable-box"> <?php //若一個單字過長，超過畫面的寬度，則將單字切斷，並換行。  ?>
                                    <a href="content_reply.php?id=<?php echo $row['id']; ?>">
                                        <p>留言已刪除</p>
                                    </a>    
                                </td>
                                <td class="msg_date msg_info">
                                    <p>刪除日期：<?php echo $row["update_at"];?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
            <?php }
        } ?>
    </div>
    <div class="wrap">
        <div class="msg text_center">
            <?php if($current_page > 1){ ?>
                <span class="text_left">
                    <a class="button_use" href="<?php echo $current_url ?>page=<?php echo $current_page-1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </span>
            <?php } ?>
            <?php for($i = 1 ; $i <= $total_page ; $i++){
                if($i == $current_page){ ?>
                    <span class="current_color"><?php echo $i ?></span>
                <?php }elseif($i == 1){ ?>
                    <span class="page-item"><a class="button_use" href="<?php echo $current_url ?>page=<?php echo $i ?>"><?php echo $i ?></a></span>
                    <?php if($i < $current_page-3){ ?>
                        <span class="page-item">...</span>
                    <?php } ?>
                <?php }elseif($i == $total_page){ ?>
                    <?php if($i > $current_page+3){ ?>
                        <span class="page-item">...</span>
                    <?php } ?>
                    <span class="page-item"><a class="button_use" href="<?php echo $current_url ?>page=<?php echo $i ?>"><?php echo $i ?></a></span>
                <?php }elseif($i > $current_page-3 && $i < $current_page+3){ ?>
                    <span class="page-item"><a class="button_use" href="<?php echo $current_url ?>page=<?php echo $i ?>"><?php echo $i ?></a></span>
                <?php } ?>
            <?php } ?>
            <?php if($current_page < $total_page){ ?>
                <span class="text_right">
                    <a class="button_use" href="<?php echo $current_url ?>page=<?php echo $current_page+1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </span>
            <?php } ?>
        </div>
        <div class="text_center">
            <p><?php echo htmlspecialchars($current_page); ?> / <?php echo htmlspecialchars($total_page); ?></p>
        </div>
    </div>
</body>
</html>