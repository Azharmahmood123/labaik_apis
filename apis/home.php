<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

requireMethod('GET');

try {
    $sectionsRes = $conn->query(" 
        SELECT
            id,
            section_key AS sectionKey,
            title,
            subtitle,
            sort_order AS sortOrder,
            visible
        FROM home_sections
        WHERE visible = 1
        ORDER BY sort_order ASC, id ASC
    ");

    if (!$sectionsRes) {
        jsonResponse(
            false,
            [],
            "Failed to fetch sections",
            500
        );
    }

    $sections = [];

    while ($section = $sectionsRes->fetch_assoc()) {
        $itemsRes = $conn->query(" 
            SELECT
                id,
                section_id AS sectionId,
                item_key AS itemKey,
                type,
                title,
                subtitle,
                icon_url AS iconUrl,
                color,
                visible
            FROM home_section_items
            WHERE section_id = " . (int) $section['id'] . "
              AND visible = 1
            ORDER BY id ASC
        ");

        if (!$itemsRes) {
            continue;
        }

        $items = [];
        while ($item = $itemsRes->fetch_assoc()) {
            $item['visible'] = (bool) $item['visible'];
            if ($item['subtitle'] === null) {
                unset($item['subtitle']);
            }
            if ($item['color'] === null) {
                unset($item['color']);
            }
            $items[] = $item;
        }

        $sections[] = [
            "id" => $section['sectionKey'],
            "title" => $section['title'],
            "subtitle" => $section['subtitle'],
            "items" => $items
        ];
    }

    jsonResponse(
        true,
        [
            "sections" => $sections
        ],
        message: "Successfully retrieved home sections"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve home sections",
        500
    );
}

