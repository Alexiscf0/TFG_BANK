// Api/groq_service.js
import { api_groq } from './api_config.js';

export async function analizarTicket(imagenBase64) {
    console.log("Iniciando Kibo Vision a través de Proxy Seguro...");

    try {
        // Llamada a nuestro propio servidor
        const response = await fetch(api_groq.proxyUrl, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ image: imagenBase64 })
        });

        if (!response.ok) throw new Error("Error en la respuesta del servidor");

        const data = await response.json();

        if (data.error) throw new Error(`Error: ${data.error.message}`);

        let content = data.choices[0].message.content.trim();

        // Limpieza y parseo del JSON
        const inicio = content.indexOf('{');
        const fin = content.lastIndexOf('}') + 1;

        return JSON.parse(content.substring(inicio, fin));

    } catch (error) {
        console.error("Fallo en el servicio de visión:", error);
        throw error;
    }
}