<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            id,
            title,
            chapter_id AS chapterId,
            chapter_name AS chapterName,
            description
        FROM life_of_usman
    ");

    if (!$result) {
        jsonResponse(
            false,
            [],
            "Failed to retrieve Uthman biography data",
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
                'title' => $title,
                'items' => []
            ];
        }

        unset($cleanedRow['title']);

        $temp[$title]['items'][] = $cleanedRow;
    }

    $response = array_values($temp);

    jsonResponse(
        true,
        [
            'sections' => $response
        ],
        message: 'Successfully retrieved Uthman biography data'
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        'Failed to retrieve Uthman biography data',
        500
    );
}