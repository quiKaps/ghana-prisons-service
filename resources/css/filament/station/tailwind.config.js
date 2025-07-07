import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Station/**/*.php',
        './resources/views/filament/station/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
