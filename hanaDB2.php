<?php

//echo 1;
//HOSTS
define('QAS', '192.168.10.113:30141');
define('PRD', '192.168.10.111:30041');
//Credentials
define('USERq', 'GUSPARED');
define('PASSq', 'Chacomer.2021');
define('PASSPRD', 'Chacomer.2022');
//DB
define('DB', 'HAQ');
define('DBP', 'HAP');
//Driver
define('DRIVER', '/opt/sap/hana/libodbcHDB.so');

//echo "Driver=".DRIVER.";ServerNode=".QAS.";Database=".DB.";CHAR_AS_UTF8=1;";    
//$qas = odbc_connect("Driver=".DRIVER.";ServerNode=".QAS.";Database=".DB.";CHAR_AS_UTF8=1;", USERq, PASSq, SQL_CUR_USE_ODBC);
//if (!$qas){
//    echo "Connection failed.\n";
//    echo "ODBC error code: " . odbc_error() . ". Message: " . odbc_errormsg();
//}

$prd = odbc_connect("Driver=".DRIVER.";ServerNode=".PRD.";Database=".DBP.";CHAR_AS_UTF8=1;", USERq, PASSPRD, SQL_CUR_USE_ODBC);
if (!$prd){
    echo "Connection failed.\n";
    echo "ODBC error code: " . odbc_error() . ". Message: " . odbc_errormsg();
}
//$prd = $qas;
