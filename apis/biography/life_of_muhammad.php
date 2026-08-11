<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            Id AS id,
            ChapterName AS chapterName,
            ChapterSubName AS chapterSubName,
            Description AS description
        FROM life_of_muhammad
        ORDER BY ChapterName ASC, Id ASC
    ");

    if (!$result) {
        jsonResponse(
            false,
            [],
            "Failed to retrieve Muhammad biography data",
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

        $chapterName = trim((string) $cleanedRow['chapterName']);

        if ($chapterName === '') {
            $chapterName = 'Others';
        }

        if (!isset($temp[$chapterName])) {
            $temp[$chapterName] = [];
        }

        unset($cleanedRow['chapterName']);

        $temp[$chapterName][] = $cleanedRow;
    }

    $response = [];

    foreach ($temp as $chapterName => $items) {
        $response[] = [
            'chapterName' => $chapterName,
            'items' => $items
        ];
    }

    jsonResponse(
        true,
        [
            'chapters' => $response
        ],
        message: 'Successfully retrieved Muhammad biography data'
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        'Failed to retrieve Muhammad biography data',
        500
    );
}