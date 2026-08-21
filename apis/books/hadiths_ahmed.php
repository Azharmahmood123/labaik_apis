<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

$request = new ApiRequest();
$validator = new Validator();

requireMethod('GET');

$chapterId = (int) ($request->getInt('chapter_id') ?? 1);

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
        FROM hadiths_ahmed
        WHERE chapter_id = ?
        ORDER BY hadith_number_in_book ASC, id ASC
    ");

    if (!$stmt) {
        jsonResponse(
            false,
            [],
            "Failed to prepare query",
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
            "total_count" => count($hadiths),
            "hadiths" => $hadiths
        ],
        message: "Successfully retrieved Ahmad hadiths"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Ahmad hadiths data",
        500
    );
}