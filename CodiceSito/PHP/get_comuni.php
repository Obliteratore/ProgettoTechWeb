<?php
header('Content-Type: application/json');

$provincia = $_GET['provincia'] ?? '';

/*$sql = "SELECT id, nome FROM comuni WHERE provincia = ? ORDER BY nome";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $provincia);
$stmt->execute();

$result = $stmt->get_result();

$comuni = [];
while ($row = $result->fetch_assoc()) {
    $comuni[] = $row;
}*/
$comuni = [
    ["id" => 1, "nome" => "Valdagno"]
];
echo json_encode($comuni);
?>