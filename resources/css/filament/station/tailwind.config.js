import preset from '../../../../vendor/filament/filament/tailwind.config.preset'
  /** @type {import('tailwindcss').Config} */

export default {
    presets: [preset],
    content: [
        './app/Filament/Station/**/*.php',
        './resources/views/filament/station/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        screens: {
          sm: '480px',
          md: '768px',
          lg: '976px',
          xl: '1440px',
        },
        colors: {
            'brown': '#654321',
            'cream': '#F9E4BC',
            'pale-blue': '#779ECB',
            'blue': '#1fb6ff',
            'purple': '#CE93D8', // updated brand purple
            'pink': '#FFC0CB',
            'orange': '#ff7849',
            'green': '#79B791',
            'yellow': '#ffc82c',
            'gray-dark': '#273444',
            'gray': '#8492a6',
            'gray-light': '#d3dce6',
            'white': '#fff',
          },
        fontFamily: {
          sans: ['Graphik', 'sans-serif'],
          serif: ['Merriweather', 'serif'],
        },
        extend: {
          spacing: {
            '128': '32rem',
            '144': '36rem',
          },
          borderRadius: {
            '4xl': '2rem',
          }
        }
      } 
    
}
