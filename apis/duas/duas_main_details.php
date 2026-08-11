<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

$request   = new ApiRequest();
$validator = new Validator();

requireMethod('GET');

$isValid = $validator->validate($request->all(), [
    'dua_category_id' => 'required|int|min:1'
]);

if (!$isValid) {
    jsonResponse(
        false,
        $validator->getErrors(),
        "Validation failed",
        400
    );
}

$categoryId = (int) $request->getInt('dua_category_id');

try {
    $stmt = $conn->prepare("
        SELECT
            dua_id AS id,
            dua_category_id AS categoryId,
            dua_arabic AS arabic,
            dua_transliteration AS transliteration,
            dua_translation AS translation,
            dua_reference AS reference
        FROM duas_detail_table
        WHERE dua_category_id = ?
        ORDER BY dua_id ASC
    ");

    $stmt->bind_param("i", $categoryId);
    $stmt->execute();

    $result = $stmt->get_result();
    $duas = fetchAll($result);
    $stmt->close();

    jsonResponse(
        true,
        [
            "dua_category_id" => $categoryId,
            "total_count" => count($duas),
            "duas" => $duas
        ],
        message: "Successfully retrieved duas for category"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve duas details data.",
        500
    );
}