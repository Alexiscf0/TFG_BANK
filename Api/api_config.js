// Api/api_config.js

// 1. CONFIGURACIÓN DE ALGOAN (Ahora solo datos públicos)
export const api_algoan = {
    authUrl: "https://auth.algoan.com/oauth2/token",
    baseUrl: "https://api.algoan.com/v1",
    mockCustomerId: "60b8f0f1e4b0f1a2b3c4d5e6"
};

// 2. CONFIGURACIÓN DE GROQ (Solo la URL de tu proxy local)
export const api_groq = {
    proxyUrl: "../Back/proxy_groq.php"
};

// 3. CONFIGURACIÓN DE NOMINATIM
export const api_maps = {
    url: "https://nominatim.openstreetmap.org/search"
};

// 4. CONFIGURACIÓN DE CURRENCY
export const api_currency = {
    url: "https://v6.exchangerate-api.com/v6/",
    key: "6ec37e351bd2779b3f51ed07"
};