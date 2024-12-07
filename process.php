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
        for ($j = 0; $j < count($matrixA[$i]); $j++) {
            $result[$i][$j] = $matrixA[$i][$j] + $matrixB[$i][$j];
        }
    }
    return $result;
}

function subtractMatrices($matrixA, $matrixB)
{
    $result = [];
    for ($i = 0; $i < count($matrixA); $i++) {
        for ($j = 0; $j < count($matrixA[$i]); $j++) {
            $result[$i][$j] = $matrixA[$i][$j] - $matrixB[$i][$j];
        }
    }
    return $result;
}

function multiplyMatrices($matrixA, $matrixB)
{
    $result = [];
    for ($i = 0; $i < count($matrixA); $i++) {
        for ($j = 0; $j < count($matrixB[0]); $j++) {
            $result[$i][$j] = 0;
            for ($k = 0; $k < count($matrixA[0]); $k++) {
                $result[$i][$j] += $matrixA[$i][$k] * $matrixB[$k][$j];
            }
        }
    }
    return $result;
}

function divideMatrices($matrixA, $matrixB)
{
    $result = [];
    for ($i = 0; $i < count($matrixA); $i++) {
        for ($j = 0; $j < count($matrixA[$i]); $j++) {
            if ($matrixB[$i][$j] == 0) {
                $result[$i][$j] = 'Infinity'; // Menangani pembagian dengan nol
            } else {
                $result[$i][$j] = $matrixA[$i][$j] / $matrixB[$i][$j];
            }
        }
    }
    return $result;
}

// Menangani operasi yang dipilih
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
        $result = ['error' => 'Operasi tidak dikenali'];
        break;
}

// Kembalikan hasil dalam format JSON
echo json_encode(['html' => matrixToHtml($result)]);
?>

<?php
// Fungsi untuk mengonversi matriks hasil menjadi HTML
function matrixToHtml($matrix) {
    $html = '<div class="matrix"><div class="content">';
    foreach ($matrix as $row) {
        $html .= '<div class="row">';
        foreach ($row as $value) {
            $html .= "<div class='cell'>$value</div>";
        }
        $html .= '</div>';
    }
    $html .= '</div></div>';
    return $html;
}
?>
