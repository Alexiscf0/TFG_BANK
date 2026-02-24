// C:\xampp\htdocs\Kibo\Api\groq_service.js
import { api_groq } from './api_config.js';

/**
 * Envía una imagen en Base64 al proxy de Groq para extraer datos del ticket.
 */
export async function analizarTicket(imagenBase64) {
    console.log("Iniciando Kibo Vision 2026 (Llama 4) vía Proxy Privado...");

    const prompt = `Analiza este ticket. Devuelve EXCLUSIVAMENTE un JSON con: 
    "concepto" (nombre tienda), "cantidad" (número decimal con punto), "categoria" (Comida, Ocio, Transporte, Hogar, Otros), "fecha" (YYYY-MM-DD).`;

    try {
        // Llamamos al proxy de PHP en lugar de a la API de Groq directamente
        const response = await fetch(api_groq.url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
                // Nota: La clave API (Authorization) ahora la pone Back/proxy_groq.php
            },
            body: JSON.stringify({
                model: api_groq.modelo,
                messages: [
                    {
                        role: "user",
                        content: [
                            { type: "text", text: prompt },
                            { type: "image_url", image_url: { url: imagenBase64 } }
                        ]
                    }
                ],
                temperature: 0.1,
                max_tokens: 1024
            })
        });

        const data = await response.json();

        if (data.error) {
            throw new Error(`Error del servidor/Groq: ${data.error.message || data.error}`);
        }

        let content = data.choices[0].message.content;

        // Limpiamos etiquetas de bloque de código si la IA las pone (```json ... ```)
        content = content.replace(/```json/g, "").replace(/```/g, "").trim();

        const inicio = content.indexOf('{');
        const fin = content.lastIndexOf('}') + 1;

        // Devolvemos el objeto JSON final procesado
        return JSON.parse(content.substring(inicio, fin));

    } catch (error) {
        console.error("Fallo en el servicio Llama 4 (Proxy):", error);
        throw error;
    }
}