<?php

//echo 1;
//HOSTS
//instancia PRD 00
//instancia QAS 01
define('QAS', '192.168.10.113:30141');
define('PRD', '192.168.10.111:30041');
//Credentials

define('USERqa', 'ODBCCONECT');
//define('PASSq', 'Chacomer2023');
define('PASSq', 'Chacomer.2024');
define('PASSPRD', 'Chacomer.2022');
//DB
define('DB', 'HAQ');
define('DBP', 'HAP');
//Driver
define('DRIVER', '/usr/sap/hdbclient/libodbcHDB.so');

$qas = odbc_connect("Driver=".DRIVER.";ServerNode=".QAS.";Database=".DB.";CHAR_AS_UTF8=1;", USERqa, PASSq, SQL_CUR_USE_ODBC);
if (!$qas){
    echo "Connection failed.\n";
    echo "ODBC error code: " . odbc_error() . ". Message: " . odbc_errormsg();
    
}

//$prd = odbc_connect("Driver=".DRIVER.";ServerNode=".PRD.";Database=".DBP.";CHAR_AS_UTF8=1;", USERq, PASSPRD, SQL_CUR_USE_ODBC);
//if (!$prd){
//    echo "Connection failed.\n";
//    echo "ODBC error code: " . odbc_error() . ". Message: " . odbc_errormsg();
//}
//$prd = $qas;
