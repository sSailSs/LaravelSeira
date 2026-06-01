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
                sans: ['Public Sans', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                canvas:          'var(--canvas)',
                surface:         'var(--surface)',
                'surface-2':     'var(--surface-2)',
                border:          'var(--border)',
                'border-strong': 'var(--border-strong)',
                ink:             'var(--ink)',
                muted:           'var(--muted)',
                faint:           'var(--faint)',
                accent:          'var(--accent)',
                'accent-hover':  'var(--accent-hover)',
                'accent-soft':   'var(--accent-soft)',
                'accent-ink':    'var(--accent-ink)',
                success:         'var(--success)',
                'success-soft':  'var(--success-soft)',
                warning:         'var(--warning)',
            },
            borderRadius: {
                xl:  '14px',
                '2xl': '18px',
            },
            boxShadow: {
                card: 'var(--shadow-card)',
                soft: 'var(--shadow-soft)',
            },
        },
    },
    plugins: [forms],
};
