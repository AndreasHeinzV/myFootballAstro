import type { APIRoute } from "astro";

export const GET: APIRoute = async ({ request }) => {
    try {

        const url = new URL(request.url);
        const leagueId = url.searchParams.get("leagueId");
        const leagueName = url.searchParams.get("leagueName");

        if (!leagueId || !leagueName) {
            return new Response(
                JSON.stringify({
                    status: "error",
                    message: "Missing 'leagueId' or 'leagueName' query parameters.",
                }),
                {
                    status: 400,
                    headers: { "Content-Type": "application/json" },
                }
            );
        }


        const symfonyApiUrl = `http://127.0.0.1:8000/league/${leagueName}/${leagueId}`;
        const response = await fetch(symfonyApiUrl, {
            method: "GET",
            headers: { "Content-Type": "application/json" },
        });


        if (!response.ok) {
            return new Response(
                JSON.stringify({
                    status: "error",
                    message: `Failed to fetch league data: ${response.statusText}`,
                }),
                {
                    status: response.status,
                    headers: { "Content-Type": "application/json" },
                }
            );
        }


        const data = await response.json();


        return new Response(JSON.stringify(data), {
            status: 200,
            headers: { "Content-Type": "application/json" },
        });
    } catch (error) {
        console.error("API Fetch Error:", error);
        return new Response(
            JSON.stringify({
                status: "error",
                message: "Failed to connect to the Symfony backend",
            }),
            { status: 500, headers: { "Content-Type": "application/json" } }
        );
    }
};
