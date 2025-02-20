import type { APIRoute } from "astro";

export const POST: APIRoute = async ({ request }) => {
    try {
        const { token } = await request.json();

        if (!token) {
            return new Response(
                JSON.stringify({
                    tokenValidation: false,
                    message: "Token is missing.",
                }),
                { status: 200, headers: { "Content-Type": "application/json" } }
            );
        }

        const response = await fetch("http://127.0.0.1:8000/reset-password/validateToken", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ token }),
        });

        const data = await response.json();

        return new Response(
            JSON.stringify({
                tokenValidation: data.tokenValidation ?? false,
                message: data.message || "Invalid token",
            }),
            { status: 200, headers: { "Content-Type": "application/json" } }
        );
    } catch (error) {
        console.error("Error validating token:", error);

        return new Response(
            JSON.stringify({
                tokenValidation: false,
                message: "An error occurred while validating the token.",
            }),
            { status: 500, headers: { "Content-Type": "application/json" } }
        );
    }
};
