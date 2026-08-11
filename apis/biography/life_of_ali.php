<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            main_id AS titleID,
            title,
            chapter_id AS chapterId,
            chapter_name AS chapterName,
            description
        FROM life_of_ali
    ");

    if (!$result) {
        jsonResponse(
            false,
            [],
            "Failed to retrieve Ali biography data",
            500
        );
    }

    $rows = fetchAll($result);
    $temp = [];

    foreach ($rows as $row) {
        $cleanedRow = [];

        foreach ($row as $key => $value) {
            $cleanedRow[$key] = $value === null ? '' : $value;
        }

        $title = trim((string) $cleanedRow['title']);

        if ($title === '') {
            $title = 'Others';
        }

        if (!isset($temp[$title])) {
            $temp[$title] = [
                'titleID' => $cleanedRow['titleID'],
                'title' => $title,
                'items' => []
            ];
        }

        unset($cleanedRow['title']);
        unset($cleanedRow['titleID']);

        $temp[$title]['items'][] = $cleanedRow;
    }

    $response = array_values($temp);

    jsonResponse(
        true,
        [
            'sections' => $response
        ],
        message: 'Successfully retrieved Ali biography data'
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        'Failed to retrieve Ali biography data',
        500
    );
}