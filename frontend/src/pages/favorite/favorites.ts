import type { APIRoute } from "astro";

export const POST: APIRoute = async ({ request }) => {
    try {
        const requestBody = await request.json();
        const token = requestBody.token;

        if (!token) {
            return new Response(
                JSON.stringify({ status: "error", message: "Missing token" }),
                { status: 400, headers: { "Content-Type": "application/json" } }
            );
        }

        const response = await fetch("http://127.0.0.1:8000/favorite-page", {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "token": token
            }
        });


        if (response.status === 204) {
            return new Response(
                JSON.stringify({ status: "success", favorites: [] }),
                { status: 200, headers: { "Content-Type": "application/json" } }
            );
        }


        const responseText = await response.text();
        const favorites = responseText ? JSON.parse(responseText) : { favorites: [] };

        return new Response(
            JSON.stringify({ status: "success", favorites }),
            { status: 200, headers: { "Content-Type": "application/json" } }
        );

    } catch (error) {
        console.log("API Fetch Error:", error);
        return new Response(
            JSON.stringify({ status: "error", message: "Failed to connect to the backend" }),
            { status: 500, headers: { "Content-Type": "application/json" } }
        );
    }
};
