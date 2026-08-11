<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';


// Initialize Request & Validator
$request   = new ApiRequest();
$validator = new Validator();

requireMethod('GET');

// Run Validation
$isValid = $validator->validate($request->all(), [
    'surah_id' => 'required|int|min:1|max:4'
]);

if (!$isValid) {
    jsonResponse(
        false,
        $validator->getErrors(),
        "Validation failed",
        400
    );
}

$surahId = $request->getInt('surah_id');

// Query Execution
$stmt = $conn->prepare("
    SELECT
        ayah_id               AS ayahId,
        surah_id              AS surahId,
        surah_ayah_index      AS ayahIndex,
        ayah_arabic           AS arabic,
        ayah_transliteration  AS transliteration,
        ayah_trans_english    AS english,
        ayah_trans_hindi      AS hindi,
        ayah_trans_indo       AS indo,
        ayah_trans_malay      AS malay,
        ayah_trans_urdu       AS urdu
    FROM four_qul
    WHERE surah_id = ?
    ORDER BY surah_ayah_index
");

$stmt->bind_param("i", $surahId);
$stmt->execute();

$result = $stmt->get_result();
$verses = fetchAll($result);
$stmt->close();

jsonResponse(
    true,
    [
        "surahId" => $surahId,
        "verses"  => $verses
    ]
    ,message: "Successfully retrieved verses"
);