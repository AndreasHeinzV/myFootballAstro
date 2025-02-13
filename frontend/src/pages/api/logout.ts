import type {APIRoute} from "astro";

export const POST: APIRoute = async ({redirect, session}) => {

  //  const token = await session?.get('token')
    // console.log(token)
    const response = await fetch('http://127.0.0.1:8000/logout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({token: await session?.get('token')}),
    })


    if (response.status === 401) {
        return new Response(JSON.stringify({
            status: 'error',
            message: 'Astro: Invalid Credentials',
        }), {status: response.status});
    }

        session?.set('token', null);
        session?.set('user', null);

    return redirect('/');

}