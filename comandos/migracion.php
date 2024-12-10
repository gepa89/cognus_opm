<?php require_once("../conect.php");

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();

/*$sql = "SELECT * FROM arti WHERE artdesc LIKE \"%\'%\" OR artdesc LIKE '%\"%'";
$res = $db->query($sql);
if (!$res) {
   print_r($db->error);
   exit;
}
while ($fila = $res->fetch_object()) {
    $descripcion = preg_replace("/\"/", "", $fila->artdesc);
    $descripcion = preg_replace("/\'/", "", $descripcion);
    $sql = "UPDATE arti SET artdesc = '$descripcion' WHERE artcodi='$fila->artcodi'";
    print_r($sql."\n");
    $db->query($sql);
}
$db->commit();*/


$searchCharacter = '%"%'; // The % character acts as a wildcard for any character

$sql = "SELECT * FROM arti WHERE artdesc LIKE ?";

$stmt = $db->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $db->error);
}

$stmt->bind_param("s", $searchCharacter);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Handle each product in the result set
        echo $row['artdesc'] . "<br>";
    }
} else {
    echo "No products found with the specified character.";
}