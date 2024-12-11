<?php
require ('conect.php');
include 'src/adLDAP.php';


$username = strtoupper($_POST["username"]); //remove case sensitivity on the username
$password = $_POST["password"];
$formage = $_POST["formage"];

$logout = $_POST['logout'];
//var_dump($logout);
if ($logout == "yes") { //destroy the session
	session_start();
	$_SESSION = array();
	session_destroy();
        
        $msg = '';
        $err = 0;
        echo json_encode(array('msg' => $msg, 'err' => $err));
        exit();
}


try {
            $adldap = new adLDAP();
} catch (adLDAPException $e) {
    echo $e; 
    exit();   
}

//authenticate the user
    if ($adldap->authenticate($username, $password)){
        //establish your session and redirect
        session_start();
        $_SESSION["username"] = $username;
        $u_info = $adldap->user()->info($username);
//        $_SESSION["userinfo"] = $u_info;        
        foreach($u_info[0]["memberof"] as $k => $v){
//            echo $k." ";
            if($k != "count"){
                $axcn = explode(',', $v);
                foreach($axcn as $k2 => $v2){
                    $axcn2 = explode('=', $v2);
                    switch ($axcn2[0]){
                        case 'CN':
                            $_SESSION["member"][$k][] = $axcn2[1];
                            break;
//                        case 'OU':
//                            $_SESSION["member"][$k2][] = $axcn2[1];
//                            break;
                    }
                }
                
            }
            
        }
        $ou = explode(',',$u_info[0]["dn"]);
        foreach($ou as $k => $v){
            $ax = explode('=', $v);
            switch ($ax[0]){
                case 'CN':
                    $_SESSION["user"] = $ax[1];
                    break;
                case 'OU':
                    $_SESSION["ou"][] = $ax[1];
                    break;
            }
        }
//        $_SESSION["userdepartament"] = $ou;
        $msg = 'Bienvenido '.$username;
        $err = 0;
    }else{
        $msg = 'Error al autenticar';
        $err = 1;
    }
require ('closeconn.php');
echo json_encode(array('msg' => $msg, 'err' => $err));

exit();
