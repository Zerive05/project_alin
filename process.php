<?php
header('Content-Type: application/json');

function matrixToHtml($matrix) {
    $html = '<div class="matrix"><div class="bracket">[</div><div class="content">';
    foreach ($matrix as $row) {
        foreach ($row as $cell) {
            $html .= '<div>' . round($cell, 2) . '</div>';
        }
    }
    $html .= '</div><div class="bracket">]</div></div>';
    return $html;
}

function validateSameSize($matrixA, $matrixB) {
    if (count($matrixA) !== count($matrixB) || count($matrixA[0]) !== count($matrixB[0])) {
        throw new Exception('Matriks harus memiliki ukuran yang sama untuk operasi ini.');
    }
}

function addMatrices($matrixA, $matrixB) {
    validateSameSize($matrixA, $matrixB);
    $result = [];
    foreach ($matrixA as $i => $row) {
        $result[] = array_map(function ($a, $b) {
            return $a + $b;
        }, $row, $matrixB[$i]);
    }
    return $result;
}

function subtractMatrices($matrixA, $matrixB) {
    validateSameSize($matrixA, $matrixB);
    $result = [];
    foreach ($matrixA as $i => $row) {
        $result[] = array_map(function ($a, $b) {
            return $a - $b;
        }, $row, $matrixB[$i]);
    }
    return $result;
}

function multiplyMatrices($matrixA, $matrixB) {
    if (count($matrixA[0]) !== count($matrixB)) {
        throw new Exception('Jumlah kolom pada Matriks A harus sama dengan jumlah baris pada Matriks B.');
    }
    $result = [];
    foreach ($matrixA as $i => $row) {
        $resultRow = [];
        for ($j = 0; $j < count($matrixB[0]); $j++) {
            $sum = 0;
            for ($k = 0; $k < count($matrixB); $k++) {
                $sum += $row[$k] * $matrixB[$k][$j];
            }
            $resultRow[] = $sum;
        }
        $result[] = $resultRow;
    }
    return $result;
}

function divideMatrices($matrixA, $matrixB) {
    validateSameSize($matrixA, $matrixB);
    foreach ($matrixB as $row) {
        foreach ($row as $value) {
            if ($value == 0) {
                throw new Exception('Pembagian dengan nol tidak diizinkan.');
            }
        }
    }
    $result = [];
    foreach ($matrixA as $i => $row) {
        $result[] = array_map(function ($a, $b) {
            return $a / $b;
        }, $row, $matrixB[$i]);
    }
    return $result;
}

function transpose($matrix) {
    return array_map(null, ...$matrix);
}

function determinant($matrix) {
    $n = count($matrix);
    if ($n !== count($matrix[0])) {
        throw new Exception('Determinan hanya berlaku untuk matriks persegi.');
    }
    if ($n == 1) {
        return $matrix[0][0];
    }
    if ($n == 2) {
        return $matrix[0][0] * $matrix[1][1] - $matrix[0][1] * $matrix[1][0];
    }
    $det = 0;
    for ($i = 0; $i < $n; $i++) {
        $minor = [];
        for ($j = 1; $j < $n; $j++) {
            $minor[] = array_values(array_diff_key($matrix[$j], [$i => $matrix[$j][$i]]));
        }
        $det += $matrix[0][$i] * determinant($minor) * ($i % 2 == 0 ? 1 : -1);
    }
    return $det;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $operation = $input['operation'];
    $matrixA = array_map(function($row) {
        return array_map('floatval', explode(',', $row));
    }, explode(';', $input['matrixA']));

    $matrixB = [];
    if (!empty($input['matrixB'])) {
        $matrixB = array_map(function($row) {
            return array_map('floatval', explode(',', $row));
        }, explode(';', $input['matrixB']));
    }

    $result = null;

    switch ($operation) {
        case 'add':
            $result = addMatrices($matrixA, $matrixB);
            break;
        case 'subtract':
            $result = subtractMatrices($matrixA, $matrixB);
            break;
        case 'multiply':
            $result = multiplyMatrices($matrixA, $matrixB);
            break;
        case 'divide':
            $result = divideMatrices($matrixA, $matrixB);
            break;
        case 'transposeA':
            $result = transpose($matrixA);
            break;
        case 'transposeB':
            $result = transpose($matrixB);
            break;
        case 'determinantA':
            $result = determinant($matrixA);
            break;
        case 'determinantB':
            $result = determinant($matrixB);
            break;
        default:
            throw new Exception('Operasi tidak dikenali.');
    }

    echo json_encode(['html' => is_array($result) ? matrixToHtml($result) : $result]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
