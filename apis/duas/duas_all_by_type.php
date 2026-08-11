<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

$request = new ApiRequest();
$validator = new Validator();

requireMethod('GET');

$allowedTables = [
    'evening' => ['table' => 'duas_for_evening_table', 'has_benefit_virtues' => true],
    'forgiveness' => ['table' => 'duas_for_forgiveness_table', 'has_benefit_virtues' => false],
    'help' => ['table' => 'duas_for_help_table', 'has_benefit_virtues' => false],
    'mercy' => ['table' => 'duas_for_mercy_table', 'has_benefit_virtues' => false],
    'morning' => ['table' => 'duas_for_morning_table', 'has_benefit_virtues' => true],
    'protection' => ['table' => 'duas_for_protection_table', 'has_benefit_virtues' => false],
    'taqwa' => ['table' => 'duas_for_taqwa_table', 'has_benefit_virtues' => false],
    'wealth' => ['table' => 'duas_for_wealth_table', 'has_benefit_virtues' => false]
];

$isValid = $validator->validate($request->all(), [
    'type' => 'required|string'
]);

if (!$isValid) {
    jsonResponse(
        false,
        $validator->getErrors(),
        "Validation failed",
        400
    );
}

$type = (string) $request->get('type');

if (!isset($allowedTables[$type])) {
    jsonResponse(
        false,
        [],
        "Invalid type parameter",
        400
    );
}

$tableConfig = $allowedTables[$type];
$tableName = $tableConfig['table'];
$hasBenefitVirtues = $tableConfig['has_benefit_virtues'];

try {
    $query = $hasBenefitVirtues
        ? "
            SELECT
                dua_id AS id,
                dua_title AS title,
                dua_arabic AS arabic,
                dua_transliteration AS transliteration,
                dua_translation AS translation,
                dua_reference AS reference,
                dua_benefit_virtues AS benefitVirtues
            FROM $tableName
            ORDER BY dua_id ASC
        "
        : "
            SELECT
                dua_id AS id,
                dua_title AS title,
                dua_arabic AS arabic,
                dua_transliteration AS transliteration,
                dua_translation AS translation,
                dua_reference AS reference
            FROM $tableName
            ORDER BY dua_id ASC
        ";

    $result = $conn->query($query);

    if (!$result) {
        jsonResponse(
            false,
            [],
            "Database error",
            500
        );
    }

    $duas = fetchAll($result);

    jsonResponse(
        true,
        [
            "type" => $type,
            "total_count" => count($duas),
            "duas" => $duas
        ],
        message: "Successfully retrieved duas by type"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve duas by type data.",
        500
    );
}