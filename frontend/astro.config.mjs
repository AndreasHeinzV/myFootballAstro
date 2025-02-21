// @ts-check
import { defineConfig } from 'astro/config';
import node from '@astrojs/node';
import tailwindcss from "@tailwindcss/vite";
export default defineConfig({
    output: 'server',

    adapter: node({
        mode: 'standalone',
    }),
    experimental: {
        session: true,
    },
    vite: {
        plugins: [tailwindcss()],  },
});