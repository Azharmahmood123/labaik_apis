<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    // 1. Fetch Steps
    $stepsResult = $conn->query("
        SELECT
            id,
            step_number AS stepNumber,
            title,
            arabic_title AS arabicTitle,
            subtitle,
            is_fardh AS isFardh,
            icon_name AS iconName,
            category,
            instruction,
            bullet_points AS bulletPoints,
            tip,
            repeat_count AS repeatCount,
            sort_order AS sortOrder
        FROM guide_wudu_steps
        ORDER BY sort_order ASC, id ASC
    ");

    if (!$stepsResult) {
        jsonResponse(
            false,
            [],
            "Failed to retrieve Wudu steps",
            500
        );
    }

    $stepsRaw = fetchAll($stepsResult);

    // 2. Fetch Recitations
    $recitationsResult = $conn->query("
        SELECT
            id,
            step_id AS stepId,
            recitation_id AS recitationId,
            title,
            arabic,
            transliteration,
            translation,
            repeat_count AS repeatCount,
            reference,
            note,
            sort_order AS sortOrder
        FROM guide_wudu_recitations
        ORDER BY step_id ASC, sort_order ASC, id ASC
    ");

    $recitationsRaw = $recitationsResult ? fetchAll($recitationsResult) : [];

    // Group recitations by stepId
    $recitationsByStep = [];
    foreach ($recitationsRaw as $rec) {
        $stepId = (int) ($rec['stepId'] ?? 0);
        if (!isset($recitationsByStep[$stepId])) {
            $recitationsByStep[$stepId] = [];
        }
        $recitationsByStep[$stepId][] = [
            'id' => (string) ($rec['recitationId'] ?? (string) $rec['id']),
            'title' => $rec['title'] ?? '',
            'arabic' => $rec['arabic'] ?? '',
            'transliteration' => $rec['transliteration'] ?? '',
            'translation' => $rec['translation'] ?? '',
            'repeatCount' => (int) ($rec['repeatCount'] ?? 1),
            'reference' => $rec['reference'] ?? null,
            'note' => $rec['note'] ?? null
        ];
    }

    // Build formatted steps list
    $steps = [];
    foreach ($stepsRaw as $s) {
        $stepId = (int) ($s['id'] ?? 0);
        $bullets = [];
        if (!empty($s['bulletPoints'])) {
            $decoded = json_decode((string) $s['bulletPoints'], true);
            $bullets = is_array($decoded) ? $decoded : [];
        }

        $steps[] = [
            'id' => $stepId,
            'stepNumber' => (string) ($s['stepNumber'] ?? ''),
            'title' => $s['title'] ?? '',
            'arabicTitle' => $s['arabicTitle'] ?? '',
            'subtitle' => $s['subtitle'] ?? '',
            'isFardh' => (bool) ((int) ($s['isFardh'] ?? 0)),
            'iconName' => $s['iconName'] ?? '',
            'category' => $s['category'] ?? 'sunnah',
            'instruction' => $s['instruction'] ?? '',
            'bulletPoints' => $bullets,
            'recitations' => $recitationsByStep[$stepId] ?? [],
            'tip' => $s['tip'] ?? null,
            'repeatCount' => (int) ($s['repeatCount'] ?? 1)
        ];
    }

    // 3. Fetch Rules (Fara'id & Sunan)
    $rulesResult = $conn->query("
        SELECT
            id,
            title,
            description,
            is_fardh AS isFardh,
            reference,
            sort_order AS sortOrder
        FROM guide_wudu_rules
        ORDER BY sort_order ASC, id ASC
    ");

    $rulesRaw = $rulesResult ? fetchAll($rulesResult) : [];
    $rules = [];
    foreach ($rulesRaw as $r) {
        $rules[] = [
            'id' => (int) ($r['id'] ?? 0),
            'title' => $r['title'] ?? '',
            'description' => $r['description'] ?? '',
            'isFardh' => (bool) ((int) ($r['isFardh'] ?? 0)),
            'reference' => $r['reference'] ?? ''
        ];
    }

    // 4. Fetch Nullifiers
    $nullifiersResult = $conn->query("
        SELECT
            id,
            title,
            description,
            icon_name AS iconName,
            sort_order AS sortOrder
        FROM guide_wudu_nullifiers
        ORDER BY sort_order ASC, id ASC
    ");

    $nullifiersRaw = $nullifiersResult ? fetchAll($nullifiersResult) : [];
    $nullifiers = [];
    foreach ($nullifiersRaw as $n) {
        $nullifiers[] = [
            'id' => (int) ($n['id'] ?? 0),
            'title' => $n['title'] ?? '',
            'description' => $n['description'] ?? '',
            'iconName' => $n['iconName'] ?? ''
        ];
    }

    // 5. Fetch Virtues
    $virtuesResult = $conn->query("
        SELECT
            id,
            title,
            hadith,
            reference,
            icon_name AS iconName,
            sort_order AS sortOrder
        FROM guide_wudu_virtues
        ORDER BY sort_order ASC, id ASC
    ");

    $virtuesRaw = $virtuesResult ? fetchAll($virtuesResult) : [];
    $virtues = [];
    foreach ($virtuesRaw as $v) {
        $virtues[] = [
            'id' => (int) ($v['id'] ?? 0),
            'title' => $v['title'] ?? '',
            'hadith' => $v['hadith'] ?? '',
            'reference' => $v['reference'] ?? '',
            'iconName' => $v['iconName'] ?? ''
        ];
    }

    jsonResponse(
        true,
        [
            'steps' => $steps,
            'rules' => $rules,
            'nullifiers' => $nullifiers,
            'virtues' => $virtues
        ],
        message: 'Successfully retrieved Wudu guide data'
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        'Failed to retrieve Wudu guide data: ' . $e->getMessage(),
        500
    );
}
