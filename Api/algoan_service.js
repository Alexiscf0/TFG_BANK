import { api_algoan } from './api_config.js';

async function getAccessToken() {
    const credentials = btoa(`${api_algoan.clientId}:${api_algoan.clientSecret}`);
    const response = await fetch(api_algoan.authUrl, {
        method: "POST",
        headers: {
            "Authorization": `Basic ${credentials}`,
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "grant_type=client_credentials"
    });
    if (!response.ok) throw new Error("Error de autenticación");
    const data = await response.json();
    return data.access_token;
}

export async function fetchBankingData() {
    try {
        const token = await getAccessToken();
        const url = `${api_algoan.baseUrl}/customers/${api_algoan.mockCustomerId}/analysis`;
        const response = await fetch(url, {
            method: "GET",
            headers: { "Authorization": `Bearer ${token}`, "Content-Type": "application/json" }
        });
        if (!response.ok) return null;
        return await response.json();
    } catch (error) {
        return null;
    }
}

export function getMockBankingData() {
    return {
        score: 745,
        analysis: {
            details: {
                expense_categories: { "Alimentación": 450, "Transporte": 85, "Vivienda": 750, "Ocio": 120 },
                income_categories: { "Nómina": 2100 },
                expense_trend: [120, 45, 180, 90, 250, 400, 150],
                income_trend: [2100, 0, 0, 0, 0, 0, 0]
            }
        }
    };
}