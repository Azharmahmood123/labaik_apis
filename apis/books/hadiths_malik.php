<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

$request   = new ApiRequest();
$validator = new Validator();

requireMethod('GET');

$isValid = $validator->validate($request->all(), [
    'chapter_id' => 'required|int|min:1'
]);

if (!$isValid) {
    jsonResponse(
        false,
        $validator->getErrors(),
        "Validation failed",
        400
    );
}

$chapterId = (int) $request->getInt('chapter_id');

try {
    $stmt = $conn->prepare(" 
        SELECT
            id,
            book_id AS bookId,
            chapter_id AS chapterId,
            hadith_number_in_book AS hadithNumberInBook,
            arabic_text AS arabicText,
            english_narrator AS englishNarrator,
            english_text AS englishText
        FROM hadiths_malik
        WHERE chapter_id = ?
        ORDER BY hadith_number_in_book ASC, id ASC
    ");

    if (!$stmt) {
        jsonResponse(
            false,
            [],
            "Failed to prepare Malik hadith query",
            500
        );
    }

    $stmt->bind_param("i", $chapterId);
    $stmt->execute();

    $result = $stmt->get_result();
    $hadiths = fetchAll($result);
    $stmt->close();

    jsonResponse(
        true,
        [
            "chapterId" => $chapterId,
            "hadiths" => $hadiths
        ],
        message: "Successfully retrieved Malik hadiths"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Malik hadiths data",
        500
    );
}
