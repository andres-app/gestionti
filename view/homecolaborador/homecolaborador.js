document.addEventListener("DOMContentLoaded", function () {

    const coloresPasteles = [
        '#A0CED9', '#FFB6B9', '#C3FBD8',
        '#F6D186', '#E2F0CB', '#CBAACB',
        '#FFF5BA'
    ];

    // Gráfico por estado
    fetch("../../controller/activo.php?op=activos_estado")
        .then(res => res.json())
        .then(data => {
            const labels = data.map(d => d.estado_nombre);
            const valores = data.map(d => parseInt(d.total));

            const ctx = document.getElementById('graficoEstado');
            if (!ctx) return;

            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad de Activos',
                        data: valores,
                        backgroundColor: coloresPasteles,
                        borderColor: '#ffffff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
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
                        backgroundColor: coloresPasteles,
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        });

    // Gráfico por sede
    fetch("../../controller/activo.php?op=activos_sede")
        .then(res => res.json())
        .then(data => {
            const labels = data.map(d => d.sede);
            const valores = data.map(d => parseInt(d.total));

            const ctx = document.getElementById('graficoSede');
            if (!ctx) return;

            new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad por Sede',
                        data: valores,
                        backgroundColor: coloresPasteles,
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#444',
                                font: { size: 13 }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#fff',
                            titleColor: '#333',
                            bodyColor: '#555',
                            borderColor: '#ccc',
                            borderWidth: 1
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
                        backgroundColor: coloresPasteles,
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        });

    // Gráfico: Obsolescencia y garantía
    fetch("../../controller/activo.php?op=obsolescencia_garantia")
        .then(response => response.json())
        .then(data => {
            const canvas = document.getElementById('graficoResumen');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Obsoletos (>=5 años)', 'Fuera de garantía (>=3 años)'],
                    datasets: [{
                        label: 'Cantidad de Equipos',
                        data: [data.obsoletos, data.fuera_garantia],
                        backgroundColor: [coloresPasteles[0], coloresPasteles[1]],
                        borderColor: '#ffffff',
                        borderWidth: 1
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
                        y: { beginAtZero: true }
                    }
                }
            });
        });

});
