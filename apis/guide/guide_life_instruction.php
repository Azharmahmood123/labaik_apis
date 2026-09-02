<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

requireMethod('GET');

try {
    $categoryParam = $_GET['category'] ?? null;
    $searchParam = $_GET['search'] ?? null;

    $query = "
        SELECT
            id,
            number,
            title,
            arabic,
            reference,
            category,
            explanation,
            sort_order AS sortOrder
        FROM guide_life_instruction_items
    ";

    $conditions = [];
    $params = [];
    $types = '';

    if (!empty($categoryParam) && $categoryParam !== 'all') {
        $conditions[] = "category = ?";
        $params[] = $categoryParam;
        $types .= 's';
    }

    if (!empty($searchParam)) {
        $searchTerm = '%' . trim($searchParam) . '%';
        $conditions[] = "(title LIKE ? OR arabic LIKE ? OR reference LIKE ? OR explanation LIKE ?)";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'ssss';
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY sort_order ASC, number ASC";

    if (!empty($params)) {
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Database prepare error: " . $conn->error);
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $itemsRaw = $result ? fetchAll($result) : [];
        $stmt->close();
    } else {
        $result = $conn->query($query);
        $itemsRaw = $result ? fetchAll($result) : [];
    }

    $instructions = [];
    foreach ($itemsRaw as $item) {
        $instructions[] = [
            'number' => (int) ($item['number'] ?? 0),
            'title' => $item['title'] ?? '',
            'arabic' => $item['arabic'] ?? '',
            'reference' => $item['reference'] ?? '',
            'category' => $item['category'] ?? 'character',
            'explanation' => $item['explanation'] ?? ''
        ];
    }

    jsonResponse(
        true,
        [
            'total' => count($instructions),
            'instructions' => $instructions
        ],
        message: 'Successfully retrieved 100 Life Instructions from the Quran'
    );
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        'Failed to retrieve life instructions: ' . $e->getMessage(),
        500
    );
} catch (Exception $e) {
    error_log($e->getMessage());

    jsonResponse(
        false,
        [],
        'An error occurred: ' . $e->getMessage(),
        500
    );
}
