<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

$request = new ApiRequest();
$validator = new Validator();

requireMethod('GET');

$isValid = $validator->validate($request->all(), [
    'surah' => 'required|int|min:1|max:144'
]);

if (!$isValid) {
    jsonResponse(
        false,
        $validator->getErrors(),
        "Validation failed",
        400
    );
}

$surah = (int) $request->getInt('surah');

try {
    $search = $surah . ':%';

    $stmt = $conn->prepare(" 
        SELECT
            id,
            ayah_key AS ayahKey,
            group_ayah_key AS groupAyahKey,
            from_ayah AS fromAyah,
            to_ayah AS toAyah,
            ayah_keys AS ayahKeys,
            text
        FROM tafsir
        WHERE ayah_key LIKE ?
        ORDER BY id ASC
    ");

    if (!$stmt) {
        jsonResponse(
            false,
            [],
            "Failed to prepare tafsir query",
            500
        );
    }

    $stmt->bind_param("s", $search);
    $stmt->execute();

    $result = $stmt->get_result();
    $data = fetchAll($result);
    $stmt->close();

    jsonResponse(
        true,
        [
            "surah" => $surah,
            "tafsir" => $data
        ],
        message: "Successfully retrieved tafsir data"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve tafsir data",
        500
    );
}
