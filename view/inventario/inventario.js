document.addEventListener('DOMContentLoaded', function () {
    let escanerActivo = false;

    cargarHistorial(); // Cargar historial al iniciar

    function iniciarEscaner() {
        if (escanerActivo) return;

        document.getElementById('preview').classList.remove('d-none'); // 👈 Asegura que se muestre el video

        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#preview'),
                constraints: { facingMode: "environment" }
            },
            decoder: {
                readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader"]
            }
        }, function (err) {
            if (err) {
                console.error("Error al iniciar Quagga:", err);
                return;
            }
            Quagga.start();
            escanerActivo = true;
            document.getElementById('btn_escanear').classList.add('d-none');
            document.getElementById('btn_detener').classList.remove('d-none');
        });
    }


    // Botón iniciar escaneo
    document.getElementById('btn_escanear').addEventListener('click', () => {
        document.getElementById('codigo_activo').value = '';
        iniciarEscaner();
    });

    // Botón detener escaneo
    document.getElementById('btn_detener').addEventListener('click', () => {
        Quagga.stop();
        document.getElementById('preview').classList.add('d-none');
        escanerActivo = false;
        document.getElementById('btn_escanear').classList.remove('d-none');
        document.getElementById('btn_detener').classList.add('d-none');
        Swal.fire('Escáner detenido', '', 'info');
    });

    // Registro de inventario
    document.getElementById('form_inventario').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('../../controller/inventario.php?op=registrar', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                Swal.fire('Registro', data.message, 'success');
                this.reset();
                cargarHistorial();
                document.getElementById('form_inventario').classList.add('d-none');
            })
            .catch(err => {
                console.error('Error al registrar inventario:', err);
                Swal.fire('Error', 'No se pudo registrar', 'error');
            });
    });

    // Mostrar/ocultar historial
    document.getElementById('btn_toggle_historial').addEventListener('click', () => {
        const wrapper = document.getElementById('historial_wrapper');
        wrapper.classList.toggle('d-none');
    });
});

// Historial
function cargarHistorial() {
    fetch('../../controller/inventario.php?op=historial')
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#tabla_historial tbody');
            tbody.innerHTML = '';

            data.forEach(item => {
                const estado = item.estado_encontrado?.toLowerCase() || '';
                let badge = '<span class="badge bg-secondary">-</span>';

                if (estado.includes('bueno')) {
                    badge = '<span class="badge bg-success">✅ Bueno</span>';
                } else if (estado.includes('regular')) {
                    badge = '<span class="badge bg-warning text-dark">⚠️ Regular</span>';
                } else if (estado.includes('malo')) {
                    badge = '<span class="badge bg-danger">❌ Malo</span>';
                }

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.codigo}</td>
                    <td>${badge}</td>
                    <td>${item.responsable || '-'}</td>
                    <td>${item.ubicacion || '-'}</td>
                    <td>${item.observaciones || '-'}</td>
                    <td>${item.fecha || '-'}</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(err => console.error('Error al cargar historial:', err));
}
