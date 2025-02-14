import type { APIRoute } from "astro";
export const POST: APIRoute = async ({ request}) => {
    try {
        const url = new URL(request.url);
        const teamId = url.searchParams.get("teamId");
        const requestBody = await request.json();

       const token = requestBody.token
        console.log('-----------------------------------------------------');
        console.log('LeagueTeamStatus Backend: ');

        console.log('Astro backend token->' + token);
        if (!teamId) {

            console.error("Missing teamId in request");
            return new Response(JSON.stringify({ status: false, message: "Missing teamId" }), {

                status: 400,
                headers: { "Content-Type": "application/json" },
            });
        }
//----------------------------------------------------------------
        // example with token in headr
        const symfonyApiUrl = `http://127.0.0.1:8000/teamDetails/favoriteStatus/${teamId}`;
        const response = await fetch(symfonyApiUrl, {
            method: "GET",
            headers: { "Content-Type": "application/json",
            'token': token},
        });
//----------------------------------------------------------------
        const data = await response.json();
        console.log("Symfony API Response:", data);
        console.log('-----------------------------------------------------');
        if (!response.ok) {
            console.error("Symfony API Error:", data.message || "Unknown error");
            return new Response(JSON.stringify({ status: false, message: data.message || "Symfony API failed" }), {
                status: response.status,
                headers: { "Content-Type": "application/json" },
            });
        }

        return new Response(JSON.stringify({ status: data.status ?? false, message: data.message || "" }), {
            status: 200,
            headers: { "Content-Type": "application/json" },
        });

    } catch (error) {
        console.error("Internal Server Error:", error);
        return new Response(JSON.stringify({ status: false, message: "Internal server error" }), {
            status: 500,
            headers: { "Content-Type": "application/json" },
        });
    }
};
