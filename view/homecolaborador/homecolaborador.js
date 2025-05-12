document.addEventListener("DOMContentLoaded", function () {

    // Gráfico por estado
    fetch("../../controller/activo.php?op=activos_estado")
        .then(res => res.json())
        .then(data => {
            const labels = data.map(d => d.estado_nombre);
            const valores = data.map(d => parseInt(d.total));

            const ctx = document.getElementById('graficoEstado');
            if (!ctx) {
                console.error("❌ No se encontró el canvas con ID 'graficoEstado'");
                return;
            }

            new Chart(ctx.getContext('2d'), {
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

    // Gráfico por tipo
    fetch("../../controller/activo.php?op=activos_tipo")
        .then(res => res.json())
        .then(data => {
            const labels = data.map(d => d.tipo);
            const valores = data.map(d => parseInt(d.total));

            const ctx = document.getElementById('graficoTipo');
            if (!ctx) return;

            new Chart(ctx.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad por Tipo',
                        data: valores,
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56',
                            '#4BC0C0', '#9966FF', '#FF9F40'
                        ],
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });

    // Gráfico por ubicación
    fetch("../../controller/activo.php?op=activos_ubicacion")
        .then(res => res.json())
        .then(data => {
            const labels = data.map(d => d.ubicacion);
            const valores = data.map(d => parseInt(d.total));

            const ctx = document.getElementById('graficoUbicacion');
            if (!ctx) return;

            new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad por Ubicación',
                        data: valores,
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56',
                            '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF'
                        ],
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });

    // Gráfico por condición
    fetch("../../controller/activo.php?op=activos_condicion")
        .then(res => res.json())
        .then(data => {
            const labels = data.map(d => d.condicion);
            const valores = data.map(d => parseInt(d.total));

            const ctx = document.getElementById('graficoCondicion');
            if (!ctx) return;

            new Chart(ctx.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Condición',
                        data: valores,
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56',
                            '#4BC0C0', '#9966FF', '#FF9F40'
                        ],
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });

    // ✅ Gráfico: Obsolescencia y garantía
    fetch("../../controller/activo.php?op=obsolescencia_garantia")
        .then(response => response.json())
        .then(data => {
            const canvas = document.getElementById('graficoResumen');
            if (!canvas) {
                console.error("❌ No se encontró el canvas 'graficoResumen'");
                return;
            }

            const ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Obsoletos (>=5 años)', 'Fuera de garantía (>=3 años)'],
                    datasets: [{
                        label: 'Cantidad de Equipos',
                        data: [data.obsoletos, data.fuera_garantia],
                        backgroundColor: ['#f87171', '#60a5fa']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        title: {
                            display: true,
                            text: 'Resumen de Obsolescencia y Garantía'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });

});
