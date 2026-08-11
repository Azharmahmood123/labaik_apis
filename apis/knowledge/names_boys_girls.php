<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

$request   = new ApiRequest();
$validator = new Validator();

requireMethod('GET');

$isValid = $validator->validate($request->all(), [
    'name_gender' => 'required|string'
]);

if (!$isValid) {
    jsonResponse(
        false,
        $validator->getErrors(),
        "Validation failed",
        400
    );
}

$gender = trim((string) $request->get('name_gender'));

try {
    $stmt = $conn->prepare("
        SELECT
            name_id       AS id,
            name_english  AS nameEnglish,
            name_meaning  AS nameMeaning,
            name_urdu     AS nameUrdu,
            name_category AS category,
            name_gender   AS gender
        FROM names_boys_girls
        WHERE name_gender = ?
        ORDER BY name_id ASC
    ");

    $stmt->bind_param("s", $gender);
    $stmt->execute();

    $result = $stmt->get_result();
    $names = fetchAll($result);
    $stmt->close();

    jsonResponse(
        true,
        [
            "gender" => $gender,
            "names"  => $names
        ],
        message: "Successfully retrieved Muslim names data"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Muslim names data.",
        500
    );
}