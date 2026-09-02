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
            category,
            icon_name AS iconName,
            instruction,
            bullet_points AS bulletPoints,
            tip,
            sort_order AS sortOrder
        FROM guide_namaz_steps
        ORDER BY sort_order ASC, id ASC
    ");

    if (!$stepsResult) {
        jsonResponse(
            false,
            [],
            "Failed to retrieve Namaz steps",
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
        FROM guide_namaz_recitations
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
            'stepNumber' => $s['stepNumber'] ?? '',
            'title' => $s['title'] ?? '',
            'arabicTitle' => $s['arabicTitle'] ?? '',
            'subtitle' => $s['subtitle'] ?? '',
            'category' => $s['category'] ?? 'standing',
            'iconName' => $s['iconName'] ?? '',
            'instruction' => $s['instruction'] ?? '',
            'bulletPoints' => $bullets,
            'recitations' => $recitationsByStep[$stepId] ?? [],
            'tip' => $s['tip'] ?? null
        ];
    }

    // 3. Fetch Daily Prayers
    $prayersResult = $conn->query("
        SELECT
            id,
            name,
            arabic_name AS arabicName,
            timing,
            icon_name AS iconName,
            sunnah_before AS sunnahBefore,
            fardh,
            sunnah_after AS sunnahAfter,
            nafl_witr AS naflWitr,
            total_rakahs AS totalRakahs,
            description,
            sort_order AS sortOrder
        FROM guide_namaz_daily_prayers
        ORDER BY sort_order ASC, id ASC
    ");

    $prayersRaw = $prayersResult ? fetchAll($prayersResult) : [];
    $prayers = [];
    foreach ($prayersRaw as $p) {
        $prayers[] = [
            'id' => (int) ($p['id'] ?? 0),
            'name' => $p['name'] ?? '',
            'arabicName' => $p['arabicName'] ?? '',
            'timing' => $p['timing'] ?? '',
            'iconName' => $p['iconName'] ?? '',
            'sunnahBefore' => $p['sunnahBefore'] ?? '',
            'fardh' => $p['fardh'] ?? '',
            'sunnahAfter' => $p['sunnahAfter'] ?? '',
            'naflWitr' => $p['naflWitr'] ?? '',
            'totalRakahs' => (int) ($p['totalRakahs'] ?? 0),
            'description' => $p['description'] ?? ''
        ];
    }

    // 4. Fetch Rakah Flows
    $flowsResult = $conn->query("
        SELECT
            id,
            title,
            rakah_count AS rakahCount,
            prayers_example AS prayersExample,
            sequence,
            sort_order AS sortOrder
        FROM guide_namaz_rakah_flows
        ORDER BY sort_order ASC, id ASC
    ");

    $flowsRaw = $flowsResult ? fetchAll($flowsResult) : [];
    $flows = [];
    foreach ($flowsRaw as $f) {
        $seq = [];
        if (!empty($f['sequence'])) {
            $decoded = json_decode((string) $f['sequence'], true);
            $seq = is_array($decoded) ? $decoded : [];
        }

        $flows[] = [
            'id' => (int) ($f['id'] ?? 0),
            'title' => $f['title'] ?? '',
            'rakahCount' => (int) ($f['rakahCount'] ?? 0),
            'prayersExample' => $f['prayersExample'] ?? '',
            'sequence' => $seq
        ];
    }

    jsonResponse(
        true,
        [
            'steps' => $steps,
            'prayers' => $prayers,
            'flows' => $flows
        ],
        message: 'Successfully retrieved Namaz guide data'
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        'Failed to retrieve Namaz guide data: ' . $e->getMessage(),
        500
    );
}
