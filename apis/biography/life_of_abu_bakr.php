<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $result = $conn->query("
        SELECT
            id,
            section_id AS sectionId,
            section_title AS sectionTitle,
            chapter_id AS chapterId,
            chapter_name AS chapterName,
            description
        FROM life_of_abu_bakr
        ORDER BY section_id ASC, chapter_id ASC
    ");

    if (!$result) {
        jsonResponse(
            false,
            [],
            "Failed to retrieve Abu Bakr biography data",
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

        $sectionId = $cleanedRow['sectionId'];

        if (!isset($temp[$sectionId])) {
            $temp[$sectionId] = [
                "sectionId" => $sectionId,
                "sectionTitle" => $cleanedRow['sectionTitle'],
                "items" => []
            ];
        }

        unset($cleanedRow['sectionId']);
        unset($cleanedRow['sectionTitle']);

        $temp[$sectionId]["items"][] = $cleanedRow;
    }

    $response = array_values($temp);

    jsonResponse(
        true,
        [
            "sections" => $response
        ],
        message: "Successfully retrieved Abu Bakr biography data"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve Abu Bakr biography data",
        500
    );
}