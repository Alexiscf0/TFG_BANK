// C:\xampp\htdocs\Kibo\Api\groq_service.js
import { api_groq } from './api_config.js';

export async function analizarTicket(imagenBase64) {
    console.log("Iniciando Kibo Vision 2026 (Llama 4)...");

    const prompt = `Analiza este ticket. Devuelve EXCLUSIVAMENTE un JSON con: 
    "concepto" (nombre tienda), "cantidad" (número decimal con punto), "categoria" (Comida, Ocio, Transporte, Hogar, Otros), "fecha" (YYYY-MM-DD).`;

    try {
        const response = await fetch(api_groq.url, {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${api_groq.key}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                // Este es el ID de modelo oficial para visión en 2026
                model: "meta-llama/llama-4-scout-17b-16e-instruct",
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
            throw new Error(`Error de Groq: ${data.error.message}`);
        }

        let content = data.choices[0].message.content;

        // Limpiamos etiquetas de bloque de código si la IA las pone
        content = content.replace(/```json/g, "").replace(/```/g, "").trim();

        const inicio = content.indexOf('{');
        const fin = content.lastIndexOf('}') + 1;

        return JSON.parse(content.substring(inicio, fin));

    } catch (error) {
        console.error("Fallo en el servicio Llama 4:", error);
        throw error;
    }
}