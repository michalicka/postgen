/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.css",
    "./resources/**/*.blade.php",
    './src/**/*.blade.php',
    './src/**/*.vue',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
