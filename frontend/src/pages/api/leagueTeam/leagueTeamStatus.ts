import type { APIRoute } from "astro";

export const GET: APIRoute = async ({ request }) => {
    try {

        const url = new URL(request.url);
        const teamId = url.searchParams.get("teamId");



        const symfonyApiUrl = `http://127.0.0.1:8000/add/${teamId}`;
        const response = await fetch(symfonyApiUrl, {
            method: "GET",
            headers: { "Content-Type": "application/json" },
        });
        const data = await response.json();

    }


    return new Response(JSON.stringify(
        status: true
    ));
}
