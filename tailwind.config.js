import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#6b46c1',
                    50: '#f6f3fb',
                    100: '#ede6f7',
                    200: '#dacdf0',
                    300: '#b79be5',
                    400: '#8b63d6',
                    500: '#6b46c1',
                    600: '#5839a3',
                    700: '#422a7a',
                    800: '#2c1c51',
                    900: '#190a28',
                },
            },
        },
    },

    plugins: [forms],
};
