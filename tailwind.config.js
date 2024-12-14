/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./node_modules/flowbite/**/*.js" // thêm dòng này
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('flowbite/plugin') // thêm plugin của Flowbite
  ],
};
