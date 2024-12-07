<?php
// Ambil data JSON dari body
$data = json_decode(file_get_contents('php://input'), true);

// Ambil operasi yang dipilih dan matriks A dan B
$operation = $data['operation'];
$matrixA = $data['matrixA'];
$matrixB = $data['matrixB'];

// Fungsi untuk operasi matriks
function addMatrices($matrixA, $matrixB)
{
    $result = [];
    for ($i = 0; $i < count($matrixA); $i++) {
        $row = [];
        for ($j = 0; $j < count($matrixA[$i]); $j++) {
            $row[] = $matrixA[$i][$j] + $matrixB[$i][$j];
        }
        $result[] = $row;
    }
    return $result;
}

function subtractMatrices($matrixA, $matrixB)
{
    $result = [];
    for ($i = 0; $i < count($matrixA); $i++) {
        $row = [];
        for ($j = 0; $j < count($matrixA[$i]); $j++) {
            $row[] = $matrixA[$i][$j] - $matrixB[$i][$j];
        }
        $result[] = $row;
    }
    return $result;
}

function multiplyMatrices($matrixA, $matrixB)
{
    $result = [];
    for ($i = 0; $i < count($matrixA); $i++) {
        $row = [];
        for ($j = 0; $j < count($matrixB[0]); $j++) {
            $sum = 0;
            for ($k = 0; $k < count($matrixA[0]); $k++) {
                $sum += $matrixA[$i][$k] * $matrixB[$k][$j];
            }
            $row[] = $sum;
        }
        $result[] = $row;
    }
    return $result;
}

function divideMatrices($matrixA, $matrixB)
{
    $result = [];
    for ($i = 0; $i < count($matrixA); $i++) {
        $row = [];
        for ($j = 0; $j < count($matrixA[$i]); $j++) {
            if ($matrixB[$i][$j] == 0) {
                $row[] = 'Infinity'; // Menangani pembagian dengan nol
            } else {
                $row[] = $matrixA[$i][$j] / $matrixB[$i][$j];
            }
        }
        $result[] = $row;
    }
    return $result;
}

// Menangani operasi yang dipilih
try {
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
        default:
            throw new Exception('Operasi tidak dikenali');
    }

    echo json_encode(['matrix' => $result]); // Pastikan format hasil sesuai
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
