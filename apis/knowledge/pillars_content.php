<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

$request   = new ApiRequest();
$validator = new Validator();

requireMethod('GET');

$isValid = $validator->validate($request->all(), [
    'bookID' => 'required|int|min:1|max:5'
]);

if (!$isValid) {
    jsonResponse(
        false,
        $validator->getErrors(),
        "Validation failed",
        400
    );
}

$bookId = (int) $request->getInt('bookID');

try {
    $stmt = $conn->prepare("
        SELECT id, chapterID, chapterTitle
        FROM pillars_chapter
        WHERE bookID = ?
        ORDER BY chapterID ASC
    ");

    if (!$stmt) {
        jsonResponse(
            false,
            [],
            "Failed to prepare chapters query",
            500
        );
    }

    $stmt->bind_param("i", $bookId);
    $stmt->execute();

    $resultChapters = $stmt->get_result();
    $response = [];

    while ($chapter = $resultChapters->fetch_assoc()) {
        $chapterId = (int) $chapter['chapterID'];
        $chapterTitle = $chapter['chapterTitle'];

        $stmtTopics = $conn->prepare("
            SELECT subTopicTitle, topicDetail
            FROM pillars_topic
            WHERE bookID = ? AND chapterID = ?
            ORDER BY id ASC
        ");

        if (!$stmtTopics) {
            continue;
        }

        $stmtTopics->bind_param("ii", $bookId, $chapterId);
        $stmtTopics->execute();

        $resultTopics = $stmtTopics->get_result();
        $chapterData = [];
        $subCounter = 1;

        while ($topic = $resultTopics->fetch_assoc()) {
            $chapterData[] = [
                "subID" => (string) $subCounter,
                "chapterName" => $chapterTitle,
                "subChapterName" => $topic['subTopicTitle'],
                "description" => $topic['topicDetail']
            ];

            $subCounter++;
        }

        $stmtTopics->close();

        if (!empty($chapterData)) {
            $response[] = [
                "id" => $chapterId,
                "title" => $chapterTitle,
                "chapterData" => $chapterData
            ];
        }
    }

    $stmt->close();

    jsonResponse(
        true,
        [
            "data" => $response
        ],
        message: "Successfully retrieved pillars content"
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        "Failed to retrieve pillars content data.",
        500
    );
}
?>