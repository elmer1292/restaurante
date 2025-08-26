<?php
// helpers/Pagination.php
// Helper para paginación segura

function getPageParams($input, $maxLimit = 50) {
    $page = isset($input['page']) ? (int)$input['page'] : 1;
    $limit = isset($input['limit']) ? (int)$input['limit'] : 10;
    if ($page < 1) $page = 1;
    if ($limit < 1) $limit = 10;
    if ($limit > $maxLimit) $limit = $maxLimit;
    $offset = ($page - 1) * $limit;
    return [
        'page' => $page,
        'limit' => $limit,
        'offset' => $offset
    ];
}
