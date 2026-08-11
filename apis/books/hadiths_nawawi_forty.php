<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

$request = new ApiRequest();

requireMethod('GET');

try {
    $stmt = $conn->prepare(" 
        SELECT
            id,
            book_id AS bookId,
            hadith_number_in_book AS hadithNumberInBook,
            arabic_text AS arabicText,
            english_narrator AS englishNarrator,
            english_text AS englishText
        FROM hadiths_nawawi_forty
        ORDER BY hadith_number_in_book ASC, id ASC
    ");

    if (!$stmt) {
        jsonResponse(
            false,
            [],
            "Failed to prepare Nawawi Forty hadith query",
            500
        );
    }

    $stmt->execute();

    $result = $stmt->get_result();
    $hadiths = fetchAll($result);
    $stmt->close();

    jsonResponse(
        true,
        [
            "hadiths" => $hadiths
        ],
        message: "Successfully retrieved Nawawi Forty hadiths"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Nawawi Forty hadiths data",
        500
    );
}
