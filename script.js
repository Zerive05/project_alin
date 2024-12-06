document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('matrixForm');
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const matrixA = document.getElementById('matrixA').value;
        const matrixB = document.getElementById('matrixB').value;
        const operation = document.querySelector('input[name="operation"]:checked').value;
        processOperation(operation, matrixA, matrixB);
    });

    document.querySelectorAll('.buttons button').forEach(button => {
        button.addEventListener('click', () => {
            const operation = button.getAttribute('onclick').match(/processOperation\('(\w+)'\)/)[1];
            const matrixA = document.getElementById('matrixA').value;
            const matrixB = document.getElementById('matrixB').value;
            processOperation(operation, matrixA, matrixB);
        });
    });
});

async function processOperation(operation) {
    const matrixA = document.getElementById('matrixA').value.trim();
    const matrixB = document.getElementById('matrixB').value.trim();

    if (!matrixA) {
        alert('Matriks A diperlukan.');
        return;
    }

    try {
        const response = await fetch('process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ operation, matrixA, matrixB }),
        });

        const result = await response.json();
        if (result.error) {
            document.getElementById('result').innerHTML = `<p style="color: red;">${result.error}</p>`;
        } else {
            document.getElementById('result').innerHTML = `<p>Hasil:<br>${result.html}</p>`;
        }
    } catch (error) {
        document.getElementById('result').innerHTML = `<p style="color: red;">Terjadi kesalahan: ${error.message}</p>`;
    }
}
