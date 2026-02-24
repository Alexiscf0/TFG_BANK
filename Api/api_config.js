// api_config.js

// 1. CONFIGURACIÓN DE ALGOAN (Banca y Salud Financiera)
export const api_algoan = {
    clientId: "c1054d80c7bd1b7a0b08b6f0", // Recuerda poner aquí el de la pestaña "Credenciales"
    clientSecret: "WXllkhR5JDBgnEXQ0xc2RFE36FgxR5gE",
    authUrl: "https://auth.algoan.com/oauth2/token",
    baseUrl: "https://api.algoan.com/v1",
    mockCustomerId: "60b8f0f1e4b0f1a2b3c4d5e6"
};

// 2. CONFIGURACIÓN DE GROQ (IA para lectura de Tickets)
// He dejado el modelo "vision" que es el que puede leer fotos
export const api_groq = {
    // Eliminamos la 'key' de aquí por seguridad
    url: "../Back/proxy_groq.php", // Ahora apuntamos al proxy local
    modelo: "meta-llama/llama-4-scout-17b-16e-instruct"
};

// 3. CONFIGURACIÓN DE MONGODB (Base de datos)
export const config_mongodb = {
    url: "mongodb+srv://alexiscastelln_db_user:LOLOKRIKO@cluster0.zfxempk.mongodb.net/KIBO?retryWrites=true&w=majority&appName=Cluster0"
};

// 4. CONFIGURACIÓN DE NOMINATIM (Mapas y Localización)
export const api_maps = {
    url: "https://nominatim.openstreetmap.org/search"
};

// 5. CONFIGURACIÓN DE CURRENCY (Cambio de Monedas)
export const api_currency = {
    url: "https://v6.exchangerate-api.com/v6/",
    key: "6ec37e351bd2779b3f51ed07"
};