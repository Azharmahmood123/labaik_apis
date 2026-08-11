<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            id,
            chapter_id AS chapterId,
            chapter_title AS chapterTitle,
            topic_title AS topicTitle,
            topic_detail AS topicDetail
        FROM guide_ramadan
        ORDER BY chapter_id ASC, id ASC
    ");

    if (!$result) {
        jsonResponse(
            false,
            [],
            "Failed to retrieve Ramadan guide data",
            500
        );
    }

    $rows = fetchAll($result);
    $grouped = [];

    foreach ($rows as $row) {
        $chapterTitle = trim((string) ($row['chapterTitle'] ?? ''));

        if ($chapterTitle === '') {
            $chapterTitle = 'Others';
        }

        if (!isset($grouped[$chapterTitle])) {
            $grouped[$chapterTitle] = [];
        }

        $grouped[$chapterTitle][] = [
            'id' => $row['id'],
            'chapterId' => $row['chapterId'],
            'chapterTitle' => $chapterTitle,
            'topicTitle' => $row['topicTitle'],
            'topicDetail' => $row['topicDetail']
        ];
    }

    $response = [];

    foreach ($grouped as $chapterTitle => $items) {
        $response[] = [
            'chapterTitle' => $chapterTitle,
            'items' => $items
        ];
    }

    jsonResponse(
        true,
        [
            'chapters' => $response
        ],
        message: 'Successfully retrieved Ramadan guide data'
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        'Failed to retrieve Ramadan guide data',
        500
    );
}
