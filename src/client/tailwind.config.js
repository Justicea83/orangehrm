// tailwind.config.js
module.exports = {
  content: [
    '.src/components/**/*.{js,vue,ts}',
    '.src/layouts/**/*.vue',
    '.src/pages/**/*.vue',
    '.src/plugins/**/*.{js,ts}',
    '.src/nuxt.config.{js,ts}',
  ],
  mode: 'jit',
  purge: ['./public/index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  darkMode: 'media', // or 'media' or 'class'
  theme: {
    extend: {
      margin: {
        hr: 0,
      },
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
};
