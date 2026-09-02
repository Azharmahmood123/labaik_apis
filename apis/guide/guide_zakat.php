<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    // 1. Fetch Topics
    $topicsResult = $conn->query("
        SELECT
            id,
            topic_id AS topicId,
            title,
            arabic_title AS arabicTitle,
            subtitle,
            category,
            icon_name AS iconName,
            description,
            bullet_points AS bulletPoints,
            quran_verse_arabic AS quranVerseArabic,
            quran_verse_translation AS quranVerseTranslation,
            quran_verse_reference AS quranVerseReference,
            hadith_text AS hadithText,
            hadith_reference AS hadithReference,
            tip,
            sort_order AS sortOrder
        FROM guide_zakat_topics
        ORDER BY sort_order ASC, id ASC
    ");

    if (!$topicsResult) {
        jsonResponse(
            false,
            [],
            "Failed to retrieve Zakat topics",
            500
        );
    }

    $topicsRaw = fetchAll($topicsResult);
    $topics = [];
    foreach ($topicsRaw as $t) {
        $bullets = [];
        if (!empty($t['bulletPoints'])) {
            $decoded = json_decode((string) $t['bulletPoints'], true);
            $bullets = is_array($decoded) ? $decoded : [];
        }

        $topics[] = [
            'id' => (string) ($t['topicId'] ?? (string) $t['id']),
            'title' => $t['title'] ?? '',
            'arabicTitle' => $t['arabicTitle'] ?? '',
            'subtitle' => $t['subtitle'] ?? '',
            'category' => $t['category'] ?? 'fundamentals',
            'iconName' => $t['iconName'] ?? '',
            'description' => $t['description'] ?? '',
            'bulletPoints' => $bullets,
            'quranVerseArabic' => $t['quranVerseArabic'] ?? null,
            'quranVerseTranslation' => $t['quranVerseTranslation'] ?? null,
            'quranVerseReference' => $t['quranVerseReference'] ?? null,
            'hadithText' => $t['hadithText'] ?? null,
            'hadithReference' => $t['hadithReference'] ?? null,
            'tip' => $t['tip'] ?? null
        ];
    }

    // 2. Fetch Recipients (8 Asnaf)
    $recipientsResult = $conn->query("
        SELECT
            id,
            number,
            title,
            arabic,
            description,
            icon_name AS iconName,
            sort_order AS sortOrder
        FROM guide_zakat_recipients
        ORDER BY sort_order ASC, number ASC, id ASC
    ");

    $recipientsRaw = $recipientsResult ? fetchAll($recipientsResult) : [];
    $recipients = [];
    foreach ($recipientsRaw as $r) {
        $recipients[] = [
            'number' => (int) ($r['number'] ?? 0),
            'title' => $r['title'] ?? '',
            'arabic' => $r['arabic'] ?? '',
            'description' => $r['description'] ?? '',
            'iconName' => $r['iconName'] ?? ''
        ];
    }

    jsonResponse(
        true,
        [
            'topics' => $topics,
            'recipients' => $recipients
        ],
        message: 'Successfully retrieved Zakat guide data'
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        'Failed to retrieve Zakat guide data: ' . $e->getMessage(),
        500
    );
}
