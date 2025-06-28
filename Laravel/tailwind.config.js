import forms from '@tailwindcss/forms';
import rtl from 'tailwindcss-rtl';

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
                vazir: ['Vazirmatn', 'sans-serif'],
            },
        },
    },

    plugins: [forms, rtl],
};

