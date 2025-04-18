/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './platform/**/resources/**/*.blade.php',
    './platform/**/resources/**/*.js',
    './platform/**/resources/**/*.vue',
    './platform/**/src/**/*.php',
  ],
  darkMode: 'class',
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
};
