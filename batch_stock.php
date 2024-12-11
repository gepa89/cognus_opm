<?php
require('conect.php');
//include 'src/adLDAP.php';
if (!isset($_SESSION['user'])) {
    header('Location:login.php');
    exit();
}
$db = new mysqli($SERVER, $USER, $PASS, $DB);
if (isset($_POST["submit"])) {

    if (isset($_FILES["file"])) {

        //if there was an error uploading the file
        if ($_FILES["file"]["error"] > 0) {
            echo "Return Code: " . $_FILES["file"]["error"] . "<br />";

        } else {

            echo "Upload: " . $_FILES["file"]["name"] . "<br />";
            echo "Type: " . $_FILES["file"]["type"] . "<br />";
            echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
            echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";
            $file = fopen($_FILES["file"]["tmp_name"], "r");
            $cc = 0;
            $fecha_ingreso = date("Y-m-d");
            $stockubi = $stockart = array();
            while (($row = fgetcsv($file, 1024, ',')) !== FALSE) {
                if ($cc == 0 || empty($row[0])) {
                    $cc++;
                    continue;
                }
                $stockubi[$row[1]][$row[0]] += $row[2];
                $stockart[$row[0]] += $row[2];
                $cant = $row[2];
                $ubi = $row[1];
                $mat = $row[0];
                $cod_alma = $row[3];
                $sqSU = "insert into stockubi set 
                canti = {$cant},
                fecingre = '{$fecha_ingreso}',
                cod_alma = '{$cod_alma}',
                artrefer = '{$mat}', 
                ubirefer = '{$ubi}', 
                etnum = 'inicial'";
                print_r($row);
                $sqSU1 = "UPDATE ubimapa set ubisitu ='LL' WHERE ubirefer = '{$ubi}'";
                $db->query($sqSU);
                $db->query($sqSU1);
            }

            //            echo "<pre>";var_dump($stockart);echo "</pre>";
            foreach ($stockart as $mat => $cant) {
                $sqSU = "insert into stockart set candispo = {$cant}, artrefer = '{$mat}', cod_alma='{$cod_alma}'";
                if (!$db->query($sqSU)) {
                    $sqSU = "update stockart set candispo = (candispo + {$cant}) where artrefer = '{$mat}'";
                    $db->query($sqSU);
                }
                //                echo "<pre>".$sqSU."<pre/>";
            }


            fclose($file);
            exit;
            //if file already exists
        }
    } else {
        echo "No file selected <br />";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'head_ds.php' ?>

<body class="full_width">
    <style>
        .details-control {
            background: url('details_open.png') no-repeat center center;
            cursor: pointer;
            width: 40px !important;
            height: 40px !important;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control,
        table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control {
            position: relative;
            padding-left: 30px;
            cursor: pointer;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr.parent>td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed>tbody>tr.parent>th.dtr-control:before {
            content: "-";
            background-color: #d33333;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control:before {
            top: 33%;
            left: 5px;
            height: 1em;
            width: 1em;
            margin-top: -5px;
            display: block;
            position: absolute;
            color: white;
            border: .15em solid white;
            border-radius: 1em;
            box-shadow: 0 0 0.2em #444;
            box-sizing: content-box;
            text-align: center;
            text-indent: 0 !important;
            font-family: "Courier New", Courier, monospace;
            line-height: 1em;
            content: "+";
            background-color: #31b131;
        }

        tr.shown td .details-control {
            background: url('details_close.png') no-repeat center center;
        }

        .label {
            color: #000;
        }

        #tblReg tbody tr td:first-of-type {
            width: 100px;
        }
    </style>
    <div id="maincontainer" class="clearfix">
        <?php include 'header.php' ?>
        <div id="contentwrapper">
            <div class="main_content">
                <div class="row">
                    <?php

                    echo '<table class="table">'
                        . '<thead>'
                        . '<tr>'
                        . '<th colspan="3">Actualización masiva de stock.</th>'
                        . '</tr>'
                        . '</thead>'
                        . '</table>'; ?>
                    <table width="600">
                        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">

                            <tr>
                                <td width="20%">Select file</td>
                                <td width="80%"><input type="file" name="file" id="file" /></td>
                            </tr>

                            <tr>
                                <td>Submit</td>
                                <td><input type="submit" name="submit" /></td>
                            </tr>

                        </form>
                    </table>
                </div>




            </div>
        </div>
    </div>
    <div style="clear:both;"></div>
    <?php
    include 'sidebar.php';
    include 'js_in.php';
    ?>
    <script type="text/javascript">

    </script>
</body>

</html>