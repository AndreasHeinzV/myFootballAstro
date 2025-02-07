import type { APIRoute } from "astro";

export const GET: APIRoute = async () => {
    try {
        const response = await fetch("http://127.0.0.1:8000/leagues", {
            method: "GET",
            // headers: { "Content-Type": "application/json" },
        });

        console.log("Response from Symfony:", response);
        if (!response.ok) {
            return new Response(
                JSON.stringify({ status: "error", message: `Failed to fetch leagues: ${response.statusText}` }),
                { status: response.status, headers: { "Content-Type": "application/json" } }
            );
        }

        const data = await response.json();

        return new Response(JSON.stringify(data), {
            status: response.status,
            headers: { "Content-Type": "application/json" },
        });
    } catch (error) {
        console.error("API Fetch Error:", error);
        return new Response(
            JSON.stringify({ status: "error", message: "Failed to connect to the backend" }),
            { status: 500, headers: { "Content-Type": "application/json" } }
        );
    }
};
