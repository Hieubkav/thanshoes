/** @type {import('tailwindcss').Config} */

import preset from './vendor/filament/support/tailwind.config.preset'

module.exports = {
  presets: [preset],
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./node_modules/flowbite/**/*.js",// thêm dòng này
    './app/Filament/**/*.php',
    './resources/views/filament/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('flowbite/plugin') // thêm plugin của Flowbite
  ],
};
