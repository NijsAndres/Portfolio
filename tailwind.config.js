import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Bai Jamjuree"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Brand coral — generated 50–950 scale, 500 pinned to the frontend accent.
                brand: {
                    50: '#fcf4f2',
                    100: '#f9e9e6',
                    200: '#f4cdc7',
                    300: '#ed9e91',
                    400: '#e86854',
                    500: '#e94e35',
                    600: '#b82a14',
                    700: '#972311',
                    800: '#7c1c0e',
                    900: '#5c150a',
                    950: '#2e0b05',
                    DEFAULT: '#e94e35',
                },
                // Warm charcoal — frontend text / footer / sidebar.
                ink: '#161513',
                // Warm cream — frontend page background.
                cream: '#f2f1ef',
            },
            boxShadow: {
                card: '0 2px 4px rgba(0, 0, 0, 0.1)',
                'card-hover': '0 8px 20px rgba(0, 0, 0, 0.12)',
            },
        },
    },

    plugins: [forms],
};
