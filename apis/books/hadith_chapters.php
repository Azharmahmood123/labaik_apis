<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

$request   = new ApiRequest();
$validator = new Validator();

requireMethod('GET');

$isValid = $validator->validate($request->all(), [
    'book_id' => 'required|int|min:1|max:17'
]);

if (!$isValid) {
    jsonResponse(
        false,
        $validator->getErrors(),
        "Validation failed",
        400
    );
}

$bookId = (int) $request->getInt('book_id');

try {
    $stmt = $conn->prepare("
        SELECT
            id,
            book_id AS bookId,
            chapter_number AS chapterNumber,
            arabic_name AS arabicName,
            english_name AS englishName
        FROM hadith_chapters
        WHERE book_id = ?
        ORDER BY chapter_number ASC, id ASC
    ");

    if (!$stmt) {
        jsonResponse(
            false,
            [],
            "Failed to prepare chapters query",
            500
        );
    }

    $stmt->bind_param("i", $bookId);
    $stmt->execute();

    $result = $stmt->get_result();
    $chapters = fetchAll($result);
    $stmt->close();

    jsonResponse(
        true,
        [
            "bookId" => $bookId,
            "chapters" => $chapters
        ],
        message: "Successfully retrieved Hadith chapters"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Hadith chapters data",
        500
    );
}
