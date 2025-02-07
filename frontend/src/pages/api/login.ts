
import type {APIRoute} from "astro";


export const POST: APIRoute = async ({request, redirect}): Promise<Response> => {
    const formData = await request.formData();

    const data = {
        username: formData.get("email")?.toString(),
        password: formData.get("password")?.toString(),

    };

    const response = await fetch('http://127.0.0.1:8000/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    });

    const responseData = await response.json();


    if (response.status === 200) {
        return new Response(JSON.stringify({
            status: 'success',
            message: 'Astro: Successfully login',
        }), {status: 200});
    }

    if (response.status === 401) {
        return new Response(JSON.stringify({
            status: 'error',
            message: responseData.message || 'Astro: Invalid Credentials',
        }), {status: response.status});
    }
    return new Response(JSON.stringify({
        status: 'error',
        message: responseData.message || 'Something went wrong',
    }), {status: response.status});
}