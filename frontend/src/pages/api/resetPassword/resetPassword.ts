import type {APIRoute} from "astro";

export const POST: APIRoute = async ({request}) => {


    const formData = await request.formData();



    const data = {
        password: formData.get("password")?.toString(),
        confirmPassword: formData.get("confirmPassword")?.toString(),
        token: formData.get("token")?.toString()
    }
    if (data.password !== data.confirmPassword) {
        return new Response(JSON.stringify({
            status: "error",
            message: "Passwords don't match",
        }), {status: 400});
    }

    const response = await fetch('http://127.0.0.1:8000/reset-password/reset/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    });

    const responseData = await response.json();

    if (response.status === 401) {
        return new Response(JSON.stringify({
            status: 'error',
            message: responseData.message || 'Astro: Something went wrong',
        }), {status: response.status});
    }

    if (response.status === 201) {
        return new Response(JSON.stringify({
            status: 'success',
            message: 'Astro: Successfully registered',
        }), {status: 200});
    }

    if (response.status === 403) {
        return new Response(JSON.stringify({
            status: 'error',
            message: 'Astro: email already registered',
        }), {status: 403});
    }
    return new Response(JSON.stringify({
        status: 'error',
        message: responseData.message || 'Something went wrong',
    }), {status: response.status});

}