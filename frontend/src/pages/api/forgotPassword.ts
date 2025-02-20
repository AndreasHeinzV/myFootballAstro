import type {APIRoute} from "astro";


export const POST: APIRoute = async ({request}) => {
    const formData = await request.formData();

    const data = {
        email: formData.get("email")?.toString(),
    };

    const response = await fetch('http://127.0.0.1:8000/reset-password/request', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    });


    const responseData = await response.json();

    if (response.status === 200) {
        return new Response(JSON.stringify({
            status: responseData.status,
            message: responseData.message,
        }), {status: 200});
    }

    if (response.status === 400) {
        return new Response(JSON.stringify({
            status: responseData.status,
            message: responseData?.message,
        }), {status: 400});
    }

    return new Response(
        JSON.stringify({
            status: "error",
            message: responseData.message || "Something went wrong",
        }), {status: response.status})

}