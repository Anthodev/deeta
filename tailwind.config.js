/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./src/Presentation/Template/**/*.html.twig",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
  daisyui: {
    themes: [],
    darkTheme: "dark",
    base: true,
    styled: true,
    utils: true,
    prefix: "",
    logs: true,
    themeRoot: ":root",
  },
}
