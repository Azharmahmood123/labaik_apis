<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    // 1. Fetch Info
    $infoResult = $conn->query("
        SELECT
            arabic,
            transliteration,
            english_translation AS englishTranslation,
            urdu_translation AS urduTranslation,
            quran_reference AS quranReference
        FROM guide_ayat_kareem_info
        LIMIT 1
    ");

    $info = null;
    if ($infoResult && $row = $infoResult->fetch_assoc()) {
        $info = [
            'arabic' => $row['arabic'] ?? '',
            'transliteration' => $row['transliteration'] ?? '',
            'englishTranslation' => $row['englishTranslation'] ?? '',
            'urduTranslation' => $row['urduTranslation'] ?? '',
            'quranReference' => $row['quranReference'] ?? ''
        ];
    }

    // 2. Fetch Pillars
    $pillarsResult = $conn->query("
        SELECT
            id,
            arabic,
            title,
            subtitle,
            icon_name AS iconName,
            color_hex AS colorHex,
            sort_order AS sortOrder
        FROM guide_ayat_kareem_pillars
        ORDER BY sort_order ASC, id ASC
    ");

    $pillarsRaw = $pillarsResult ? fetchAll($pillarsResult) : [];
    $pillars = [];
    foreach ($pillarsRaw as $p) {
        $pillars[] = [
            'id' => (int) ($p['id'] ?? 0),
            'arabic' => $p['arabic'] ?? '',
            'title' => $p['title'] ?? '',
            'subtitle' => $p['subtitle'] ?? '',
            'iconName' => $p['iconName'] ?? 'auto_awesome_rounded',
            'colorHex' => $p['colorHex'] ?? '#0C613C'
        ];
    }

    // 3. Fetch Sections
    $sectionsResult = $conn->query("
        SELECT
            id,
            section_id AS sectionId,
            title,
            arabic_title AS arabicTitle,
            icon_name AS iconName,
            description,
            bullet_points AS bulletPoints,
            tip,
            sort_order AS sortOrder
        FROM guide_ayat_kareem_sections
        ORDER BY sort_order ASC, id ASC
    ");

    $sectionsRaw = $sectionsResult ? fetchAll($sectionsResult) : [];
    $sections = [];
    foreach ($sectionsRaw as $s) {
        $bullets = [];
        if (!empty($s['bulletPoints'])) {
            $decoded = json_decode((string) $s['bulletPoints'], true);
            $bullets = is_array($decoded) ? $decoded : [];
        }

        $sections[] = [
            'id' => (string) ($s['sectionId'] ?? (string) $s['id']),
            'title' => $s['title'] ?? '',
            'arabicTitle' => $s['arabicTitle'] ?? '',
            'iconName' => $s['iconName'] ?? 'menu_book_rounded',
            'description' => $s['description'] ?? '',
            'bulletPoints' => $bullets,
            'tip' => $s['tip'] ?? null
        ];
    }

    jsonResponse(
        true,
        [
            'info' => $info,
            'pillars' => $pillars,
            'sections' => $sections
        ],
        message: 'Successfully retrieved Ayat-e-Karima guide data'
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        'Failed to retrieve Ayat-e-Karima guide data: ' . $e->getMessage(),
        500
    );
}
