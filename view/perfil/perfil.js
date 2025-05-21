// Esperar 3 segundos y desaparecer el mensaje
    setTimeout(function () {
        const mensaje = document.getElementById("mensaje-actualizado");
        if (mensaje) {
            mensaje.style.transition = "opacity 0.5s ease-out";
            mensaje.style.opacity = 0;
            setTimeout(() => mensaje.remove(), 500); // Elimina el div después del fade out
        }
    }, 3000);