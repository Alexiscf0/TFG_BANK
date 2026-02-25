import { api_groq } from './api_config.js';

export async function analizarTicket(imagenBase64) {
    const prompt = `Analiza este ticket. Devuelve EXCLUSIVAMENTE un JSON con: 
    "concepto" (nombre tienda), "cantidad" (número decimal con punto), "categoria" (Comida, Ocio, Transporte, Hogar, Otros), "fecha" (YYYY-MM-DD).`;

    try {
        const response = await fetch(api_groq.url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                model: api_groq.modelo,
                messages: [{
                    role: "user",
                    content: [
                        { type: "text", text: prompt },
                        { type: "image_url", image_url: { url: imagenBase64 } }
                    ]
                }],
                temperature: 0.1
            })
        });

        const data = await response.json();
        let content = data.choices[0].message.content;
        content = content.replace(/```json/g, "").replace(/```/g, "").trim();
        const inicio = content.indexOf('{');
        const fin = content.lastIndexOf('}') + 1;
        return JSON.parse(content.substring(inicio, fin));
    } catch (error) {
        throw error;
    }
}