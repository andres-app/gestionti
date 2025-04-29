document.addEventListener("DOMContentLoaded", function () {
    fetch("../../controller/activo.php?op=activos_estado")
        .then(res => res.json())
        .then(data => {
            console.log("📊 Datos recibidos:", data); // Verifica si esto se imprime

            const labels = data.map(d => d.estado_nombre);
            const valores = data.map(d => parseInt(d.total));

            const ctx = document.getElementById('graficoEstado');
            if (!ctx) {
                console.error("❌ No se encontró el canvas con ID 'graficoEstado'");
                return;
            }

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad de Activos',
                        data: valores,
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.5)',
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(255, 205, 86, 0.5)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 205, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
});

fetch("../../controller/activo.php?op=activos_tipo")
    .then(res => res.json())
    .then(data => {
        const labels = data.map(d => d.tipo);
        const valores = data.map(d => parseInt(d.total));

        const ctx = document.getElementById('graficoTipo');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Cantidad por Tipo',
                    data: valores,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(153, 102, 255, 0.5)',
                        'rgba(255, 159, 64, 0.5)'
                    ],
                    borderColor: 'rgba(255, 255, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });
    });
