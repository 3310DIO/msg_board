<?php
    require 'db.php';

    switch($_GET["method"]){ // 根據得到的method種類選擇使用的function
        case "login":
            login($pdo);
            break;
        case "register":
            register($pdo);
            break;
        case "logout":
            logout();
            break;
        case "set_account":
            set_account($pdo);
            break;
        default:
            break;
    }
    function login($pdo){ // 登錄使用
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            // global $pdo;
            $user_account = $_POST['user_account'];
            $user_password = $_POST['user_password'];
            $sql = "SELECT user_id, user_account, user_name, user_password FROM member WHERE user_account = ? ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_account]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC); // 從資料庫中抓取輸入的帳號資料
            $name_check = $row['user_account'] ?? '';
            if(($name_check != '')){  // 判斷輸入的帳號是否存在於資料庫
                if(password_verify($user_password, $row["user_password"])){ //  將輸入的帳號跟資料庫中的加密資料做比對   //($row["user_password"] == $user_password)
                    session_start();
                    $_SESSION["user_account"] = $user_account;
                    $_SESSION["user_name"] = $row['user_name'];
                    echo "<script>";
                    echo "alert('登入成功');";
                    echo "location.href='index.php';";
                    // echo "window.history.go(-2);";
                    echo "</script>";
                }else{
                    echo "<script>";
                    echo "alert('密碼錯誤，請重新輸入');";
                    echo "location.href='login.php';";
                    echo "</script>";
                }
            }else{
                // header('refresh:5;url=index.php');
                echo "<script>";
                echo "alert('帳號不存在，請重新輸入');";
                echo "location.href='login.php';";
                echo "</script>";
            }
            
        }
    }
    function register($pdo){ // 註冊使用
        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_account']) && isset($_POST['user_name']) && isset($_POST['user_password'])){
            // global $pdo;
            // if(isset($_POST['user_account']) || isset($_POST['user_name']) || isset($_POST['user_password'])){
            //     echo "<script>";
            //     echo "alert('請輸入帳號、暱稱及密碼');";
            //     echo "location.href='register.php';";
            //     echo "</script>";
            // }else{
                $user_account = $_POST['user_account'];
                $user_name = $_POST['user_name'];
                $user_password = $_POST['user_password'];
                if($user_account == '' || $user_name == '' || $user_password == ''){ // 判斷輸入的帳號、暱稱、密碼是否為空
                    echo "<script>";
                    echo "alert('禁止輸入空字元');";
                    echo "location.href='register.php';";
                    echo "</script>";
                }else{
                    if(preg_match('/^[a-zA-Z0-9]{8,20}$/', $user_account) && preg_match('/^.{2,20}$/', $user_name)){ // 檢查輸入的帳號與暱稱是否符合規定
                        if(preg_match('/^(?=.*\d)(?=.*[a-zA-Z])(?=.*\W).{8,25}$/', $user_password)){ //  檢查密碼是否符合條件   // ^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@!~%^&*])[a-z\d#@!~%^&*]{8,25}$
                            $sql = "SELECT user_account FROM member WHERE user_account = ? ";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute(["$user_account"]);
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            if($row!=""){ // 帳號若已存在於資料庫中則跳入登錄頁面
                                echo "<script>";
                                echo "alert('該帳號已存在');";
                                echo "location.href='login.php';";
                                echo "</script>";
                            }else{ // 將輸入的密碼加密後與帳號跟暱稱從入資料庫
                                $password_hash = password_hash($user_password, PASSWORD_DEFAULT);
                                $sql = "INSERT INTO member(user_account, user_name, user_password) VALUES (?, ?, ?)"; // VALUES (?,?) 使用?代表輸入值。
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute([$user_account, $user_name, $password_hash]); // array($msg_name, $title, $content) 將獲取的值輸入進?中。
                                session_start();
                                $_SESSION["user_account"] = $user_account;
                                $_SESSION["user_name"] = $user_name;
                                header('refresh:0;url=index.php');
                                exit;
                            }
                        }else{
                            echo "<script>";
                            echo "alert('密碼需在8~25字間，且密碼須包含英文大小寫、數字及特殊符號');";
                            echo "location.href='register.php';";
                            echo "</script>";
                        }
                    }else{
                        echo "<script>";
                        echo "alert('帳號需在8~20字間且為英文或數字，暱稱需在2~20字間');";
                        echo "location.href='register.php';";
                        echo "</script>";
                    }
                }
            // }
        }else{
            echo "<script>";
            echo "alert('請輸入帳號、暱稱及密碼');";
            echo "location.href='register.php';";
            echo "</script>";
        }
    }
    function logout(){ // 登出使用
        // require 'db.php';
        session_start();
        if(isset($_SESSION["user_name"])){ // 將全域變數中的帳號及暱稱清除
            session_unset();
            echo "<script>";
            echo "alert('登出成功');";
            echo "location.href='index.php';";
            echo "</script>";
        }
    }
    function set_account($pdo){ // 設定帳號
        if(!($_SERVER["REQUEST_METHOD"] == "POST")){
            echo "<script>";
            echo "alert('請輸入帳號、暱稱及密碼');";
            echo "location.href='register.php';";
            echo "</script>";
        }else{
            session_start();
            // global $pdo;
            // if(isset($_POST['user_account']) || isset($_POST['user_name']) || isset($_POST['user_password'])){
            //     echo "<script>";
            //     echo "alert('請輸入帳號、暱稱及密碼');";
            //     echo "location.href='register.php';";
            //     echo "</script>";
            // }else{
            $user_account = $_SESSION['user_account'];
            $user_name = $_POST['user_name'] ?? '';
            $user_password_old = $_POST['user_password_old'];
            $user_password_new = $_POST['user_password_new'];
            
            $sql = "SELECT user_name, user_password FROM member WHERE user_account = ? ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["$user_account"]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if($user_password_new == ''){
                if($user_name == '' || $user_name == $row["user_name"]){ // 若沒有輸入新的暱稱則將舊的暱稱設為參數
                    echo "<script>";
                    echo "alert('請輸入修改內容');";
                    echo "location.href='setting_account.php';";
                    echo "</script>";
                }else{
                    if(!(preg_match('/^.{2,20}$/', $user_name))){
                        echo "<script>";
                        echo "alert('暱稱需在2~20字間');";
                        echo "location.href='setting_account.php';";
                        echo "</script>";
                    }else{
                        $sql = "UPDATE member SET user_name = ? WHERE user_account = ? "; // VALUES (?,?) 使用?代表輸入值。
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(["$user_name","$user_account"]); // array($msg_name, $title, $content) 將獲取的值輸入進?中。
                        session_start();
                        $_SESSION["user_account"] = $user_account;
                        $_SESSION["user_name"] = $user_name;
                        echo "<script>";
                        echo "alert('暱稱修改成功');";
                        echo "location.href='index.php';";
                        echo "</script>";
                        // header('refresh:0;url=index.php');
                        exit;
                    }
                }
            }else{
                if($user_name == '' || $user_name == $row["user_name"]){ // 若沒有輸入新的暱稱則將舊的暱稱設為參數
                    $user_name = $row["user_name"];
                }
                if(mb_strlen($user_name) > 20 || mb_strlen($user_name) < 2){ // 檢查暱稱是否符合條件
                    echo "<script>";
                    echo "alert('暱稱需在2~20字間');";
                    echo "location.href='setting_account.php';";
                    echo "</script>";
                }else{
                    if(!(password_verify($user_password_old, $row["user_password"]))){ // 檢查密碼是否與舊密碼相同
                        echo "<script>";
                        echo "alert('密碼錯誤');";
                        echo "location.href='setting_account.php';";
                        echo "</script>";
                    }else{
                        if(password_verify($user_password_new, $row["user_password"])){ // 檢查新密碼是否與舊密碼相同
                            echo "<script>";
                            echo "alert('新密碼需與舊密碼不同');";
                            echo "location.href='setting_account.php';";
                            echo "</script>";
                        }else{
                            if(!(preg_match('/^(?=.*\d)(?=.*[a-zA-Z])(?=.*\W).{8,25}$/', $user_password_new))){ // 檢查密碼是否符合條件   //  ^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@!~%^&*])[a-z\d#@!~%^&*]{8,25}$
                                echo "<script>";
                                echo "alert('密碼需在8~25字間，且密碼須包含英文大小寫、數字及特殊符號');";
                                echo "location.href='setting_account.php';";
                                echo "</script>";
                            }else{
                                $password_hash = password_hash($user_password_new, PASSWORD_DEFAULT);
                                $sql = "UPDATE member SET user_name = ? , user_password = ? WHERE user_account = ? "; // VALUES (?,?) 使用?代表輸入值。
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute([$user_name, $password_hash, $user_account]); // array($msg_name, $title, $content) 將獲取的值輸入進?中。
                                session_start();
                                // $_SESSION["user_account"] = $user_account;
                                // $_SESSION["user_name"] = $user_name;
                                session_unset();
                                echo "<script>";
                                echo "alert('密碼修改成功請重新登錄');";
                                echo "location.href='index.php';";
                                echo "</script>";
                                // header('refresh:0;url=index.php');
                                // exit;
                            }
                        }
                    }
                }
            }
            // }
        }
    }

?>