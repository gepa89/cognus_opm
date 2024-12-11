<?php
require_once(__DIR__ . "/../database/database.php");
require_once(__DIR__ . "/../database/hana.php");
require_once(__DIR__ . "/../database/SQLBuilder.php");

$db = MysqlDB::obtenerInstancia();
$conn_odbc = HanaDB::obtenerInstancia();
$db->begin_transaction();
$centro = 'CHEL';
$aml = 'CD11';
$sqhan = "SELECT mara.laeda, mara.matnr, mara.prdha, mara.matkl, mara.meins, 
            marc.SERNP, 
            MAKT.MAKTX,
			ZMMCLASE_VAL.BWTAR
            FROM SAPABAP1.MARD
            LEFT JOIN SAPABAP1.MARA ON MARD.MATNR=MARA.MATNR 
            INNER JOIN SAPABAP1.MARC ON MARD.MATNR=MARC.MATNR AND MARD.WERKS=MARC.WERKS 
            INNER JOIN SAPABAP1.MAKT ON MARD.MATNR = MAKT.MATNR AND MAKT.SPRAS ='S'
            INNER JOIN SAPABAP1.ZMMCLASE_VAL ON MARD.MATNR = ZMMCLASE_VAL.MATNR AND MARD.WERKS=ZMMCLASE_VAL.WERKS  
            AND MARD.WERKS ='CHEL'
            AND MARD.LGORT = 'CD11'   
            AND MAKT.SPRAS ='S'
            AND MARA.LAEDA =  TO_CHAR( CURRENT_DATE, 'YYYYMMDD' )";

$rst = odbc_exec($conn_odbc, $sqhan);
if (!$rst) {
    print_r("ocurrio error  odbc");
    print_r(odbc_error());
    exit;
}

while ($rw = odbc_fetch_object($rst)) {
    $cod = $rw->MATNR;
    $sql = "SELECT * FROM arti WHERE artrefer='{$cod}' AND almcod = '{$aml}'";
    $res = $db->query($sql);
    if (!$res) {
        guardar_error_log("ingreso_articulos", $sql, $db->error);
    }
    if ($res->num_rows > 0) {
        continue;
    }

    $sqlBuilder = new MySqlQuery($db);
    $parametros = [
        'artrefer' => htmlspecialchars($cod),
        'artdesc' => htmlspecialchars($rw->MAKTX),
        'unimed' => htmlspecialchars($rw->MEINS),
        'artser' => htmlspecialchars($rw->SERNP),
        'artgrup' => htmlspecialchars($rw->MATKL),
        'artjerar' => htmlspecialchars($rw->PRDHA),
        'artval' => htmlspecialchars($rw->BWTAR),
        'almcod' => htmlspecialchars($aml),
        'fecaut' => date('Y-m-d H:i:s')
    ];
    $sql = $sqlBuilder->table('arti')->buildInsert($parametros)->execute();
    $sqhanX = "SELECT mara.matnr,
                        mara.laeda, 
                        mean.ean11 

                        FROM SAPABAP1.MARA, SAPABAP1.MARC, SAPABAP1.Mean
                        WHERE MARC.WERKS= '" . $centro . "'

                        AND MARA.MATNR=MARC.MATNR
                        AND MARA.LAEDA =  TO_CHAR( CURRENT_DATE, 'YYYYMMDD' )
                        AND mara.matnr=mean.matnr
                        AND mara.matnr = '{$cod}'";

    $odbc_res = odbc_exec($conn_odbc, $sqhanX);

    while ($rw = odbc_fetch_object($odbc_res)) {
        $sqlBuilder = new MySqlQuery($db);
        $parametros = [
            'artrefer' => htmlspecialchars($cod),
            'cod_alma' => htmlspecialchars($aml),
            'ean' => htmlspecialchars($rw->EAN11),
            'fecaut' => date('Y-m-d H:i:s')
        ];


        $sql = $sqlBuilder->table('artean')->buildInsert($parametros)->execute();

        $sqlBuilder = new MySqlQuery($db);
        $parametros = [
            'artrefer' => htmlspecialchars($cod),
            'cod_alma' => htmlspecialchars($aml),
            'canpresen' => 1,
            'preseref' => 'UNI'
        ];
        $where = [
            'artrefer' => htmlspecialchars($cod),
            'cod_alma' => htmlspecialchars($aml),
            'preseref' => 'UNI'
        ];
        $presentacion = $sqlBuilder->table('artipresen')->select('*')->where($parametros)
            ->executeSelect()
            ->getOne();
        if (!$presentacion) {
            $sql = $sqlBuilder->table('artipresen')->buildInsert($parametros)->execute();
        }
    }
}

$scriptname = basename(__FILE__, '.php');
$sqlBuilder = new MySqlQuery($db);
$where = [
    'script' => $scriptname
];
$resultado = $sqlBuilder->table('scheduled_jobs')->select('*')->where($where)->executeSelect()->getOne();
if ($resultado) {
    $sqlBuilder = new MySqlQuery($db);
    $parametros = [
        'last' => date('Y-m-d H:i:s')
    ];
    $sql = $sqlBuilder->table('scheduled_jobs')->buildUpdate($parametros)->where($where)->executeUpdate();
} else {
    $sqlBuilder = new MySqlQuery($db);
    $parametros = [
        'script' => $scriptname,
        'last' => date('Y-m-d H:i:s')
    ];
    $sql = $sqlBuilder->table('scheduled_jobs')->buildInsert($parametros)->execute();
}
$db->commit();
$db->close();
HanaDB::cerrarConexion($conn_odbc);
