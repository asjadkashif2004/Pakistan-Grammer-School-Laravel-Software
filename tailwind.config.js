import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    /* Keep paginator layout utilities in production builds (avoids giant SVG / duplicate rows) */
    safelist: [
        'hidden',
        'sm:hidden',
        'sm:flex',
        'sm:flex-1',
        'sm:items-center',
        'sm:justify-between',
        'sm:gap-2',
        'w-5',
        'h-5',
        'flex',
        'gap-2',
        'items-center',
        'justify-between',
        'inline-flex',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
