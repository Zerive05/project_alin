document.addEventListener('DOMContentLoaded', () => {
    const matrixContainerA = document.getElementById('matrixContainerA');
    const matrixContainerB = document.getElementById('matrixContainerB');
    const rowsInputA = document.getElementById('rowsA');
    const colsInputA = document.getElementById('colsA');
    const rowsInputB = document.getElementById('rowsB');
    const colsInputB = document.getElementById('colsB');
    const generateMatrixAButton = document.getElementById('generateMatrixA');
    const generateMatrixBButton = document.getElementById('generateMatrixB');
    const resultContainer = document.getElementById('result');

    // Fungsi untuk membuat input grid matriks
    function createMatrixGrid(container, rows, cols) {
        container.innerHTML = ''; // Hapus grid sebelumnya
        const grid = document.createElement('div');
        grid.className = 'grid';
        grid.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;

        for (let i = 0; i < rows; i++) {
            for (let j = 0; j < cols; j++) {
                const input = document.createElement('input');
                input.type = 'number';
                input.step = 'any'; // Untuk mendukung angka desimal
                input.dataset.row = i;
                input.dataset.col = j;
                grid.appendChild(input);
            }
        }

        container.appendChild(grid);
    }

    // Event listener untuk membuat Matriks A
    generateMatrixAButton.addEventListener('click', () => {
        const rows = parseInt(rowsInputA.value, 10) || 0;
        const cols = parseInt(colsInputA.value, 10) || 0;

        if (rows > 0 && cols > 0) {
            createMatrixGrid(matrixContainerA, rows, cols);
        } else {
            alert('Masukkan jumlah baris dan kolom yang valid untuk Matriks A.');
        }
    });

    // Event listener untuk membuat Matriks B
    generateMatrixBButton.addEventListener('click', () => {
        const rows = parseInt(rowsInputB.value, 10) || 0;
        const cols = parseInt(colsInputB.value, 10) || 0;

        if (rows > 0 && cols > 0) {
            createMatrixGrid(matrixContainerB, rows, cols);
        } else {
            alert('Masukkan jumlah baris dan kolom yang valid untuk Matriks B.');
        }
    });

    // Fungsi untuk membaca nilai matriks dari grid
    function getMatrixValues(container) {
        const inputs = container.querySelectorAll('input');
        const matrix = [];
        inputs.forEach((input, index) => {
            const rowIndex = Math.floor(index / container.style.gridTemplateColumns.split(' ').length);
            if (!matrix[rowIndex]) {
                matrix[rowIndex] = [];
            }
            matrix[rowIndex].push(parseFloat(input.value) || 0); // Default 0 jika input kosong
        });
        return matrix;
    }


    // Perbarui fungsi processOperation
    async function processOperation(operation) {
        const matrixA = getMatrixValues(matrixContainerA);
        const matrixB = getMatrixValues(matrixContainerB);

        if (!matrixA.length || !matrixB.length) {
            alert('Matriks A dan B diperlukan.');
            return;
        }

        const data = { operation, matrixA, matrixB };
        console.log("Mengirim data:", data); // Debug log

        try {
            const response = await fetch('process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            });

            const result = await response.json();
            console.log("Hasil dari server:", result); // Debug log

            if (result.error) {
                resultContainer.innerHTML = `<p style="color: red;">${result.error}</p>`;
            } else {
                // Hapus hasil sebelumnya
                resultContainer.innerHTML = '';

                // Render grid hasil matriks
                const matrixResult = result.matrix; // Pastikan server mengembalikan array 2D
                const resultGrid = displayMatrix(matrixResult);

                resultContainer.appendChild(resultGrid); // Tambahkan ke kontainer hasil
            }
        } catch (error) {
            resultContainer.innerHTML = `<p style="color: red;">Terjadi kesalahan: ${error.message}</p>`;
        }
    }

    // Event listener untuk tombol operasi
    document.querySelectorAll('.buttons button').forEach(button => {
        button.addEventListener('click', () => {
            const operation = button.getAttribute('data-operation');
            console.log(`Tombol operasi: ${operation}`); // Debug log
            processOperation(operation);
        });
    });

    // Fungsi untuk membuat elemen grid hasil matriks
    function displayMatrix(matrix) {
        const rows = matrix.length;
        const cols = matrix[0].length;

        const grid = document.createElement('div');
        grid.className = 'grid';
        grid.style.gridTemplateColumns = `repeat(${cols}, 1fr)`; // Set jumlah kolom sesuai matriks

        matrix.forEach(row => {
            row.forEach(value => {
                const cell = document.createElement('div');
                cell.className = 'cell';
                cell.textContent = value; // Tampilkan nilai di setiap cell
                grid.appendChild(cell);
            });
        });

        return grid;
    }
});
