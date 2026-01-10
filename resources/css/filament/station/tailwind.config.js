import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

/** @type {import('tailwindcss').Config} */
export default {
    presets: [preset],
    content: [
        './app/Filament/Station/**/*.php',
        './resources/views/filament/station/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './resources/css/filament/station/**/*.css',
    ],
    theme: {
        extend: {
            colors: {
                'brown': '#654321',
                'cream': '#F9E4BC',
                'blue': '#1fb6ff', // Matches the @apply bg-blue in your CSS
                'pale-blue': '#779ECB',
                'purple': '#CE93D8',
                'pink': '#FFC0CB',
                'orange': '#ff7849',
                'green': '#79B791',
                'yellow': '#ffc82c',
                'gray-dark': '#273444',
                'gray-custom': '#8492a6',
                'gray-light': '#D3DCE6',
                'white': '#fff',
                'khaki': '#F7F1F0',
                'corn': '#FFF380',
                'parchment': '#FFFFC2',
                'tan': '#D2B48C',
                'camel': '#C19A6B',
            },
        },
    },
}