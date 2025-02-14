import type {APIRoute} from "astro";

export const POST: APIRoute = async ({request, session}) => {
    try {
        const teamId = await request.json();
        console.log("The data ->" + teamId);

        const token = await session?.get("token");
        console.log("token-> " + token);

        // Validate input
        if (!teamId || !token) {
            return new Response(
                JSON.stringify({message: "Missing teamId or token"}),
                {status: 400, headers: {"Content-Type": "application/json"}}
            );
        }

        const addToFavoritesResponse = await fetch("http://localhost:8000/teamDetails/remove", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                'token': token,
            },
            body: JSON.stringify({teamId}),
        });

        if (!addToFavoritesResponse.ok) {

            const data = await addToFavoritesResponse.json();

            return new Response(
                JSON.stringify({message: "Failed to add team to favorites" + data.message}),
                {status: 500, headers: {"Content-Type": "application/json"}}
            );
        }

        return new Response(
            JSON.stringify({message: "Team successfully added to favorites"}),
            {status: 200, headers: {"Content-Type": "application/json"}}
        );

    } catch (error) {
        console.error("Error in add.ts:", error);
        return new Response(
            JSON.stringify({message: "Internal server error"}),
            {status: 500, headers: {"Content-Type": "application/json"}}
        );
    }
};

