/** @type {import('tailwindcss').Config} */
export default {
    content: ['./src/**/*.{astro,html,ts, js, jsx, tsx}'],
    theme: {
        extend: {
            fontFamily: {
                default: ['Roboto', 'sans-serif'],
            },
            colors: {
                dark: {
                    background: '#17181c',
                    bts: '#507526',
                },
            },
        },
    },
    plugins: [],
};