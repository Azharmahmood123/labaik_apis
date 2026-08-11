<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

$request   = new ApiRequest();
$validator = new Validator();

requireMethod('GET');

$isValid = $validator->validate($request->all(), [
    'saying_category' => 'required|int|min:1|max:4'
]);

if (!$isValid) {
    jsonResponse(
        false,
        $validator->getErrors(),
        "Validation failed",
        400
    );
}

$category = (int) $request->getInt('saying_category');

try {
    $stmt = $conn->prepare("
        SELECT
            saying_id AS id,
            saying_category AS category,
            saying_text AS text
        FROM sayings_sahaba
        WHERE saying_category = ?
        ORDER BY saying_id ASC
    ");

    $stmt->bind_param("s", $category);
    $stmt->execute();

    $result = $stmt->get_result();
    $data = fetchAll($result);
    $stmt->close();

    jsonResponse(
        true,
        [
            "category" => $category,
            "sayings" => $data
        ],
        message: "Successfully retrieved Sahaba sayings"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Sahaba sayings data.",
        500
    );
}