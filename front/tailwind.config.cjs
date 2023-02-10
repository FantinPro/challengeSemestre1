/** @type {import('tailwindcss').Config} */

const formKitTailwind = require('@formkit/themes/tailwindcss');

module.exports = {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        'row-table': '#2e3944',
        'row-table-hover': '#2f4a5f',
        error: '#FF5B5B',
        primary: {
          900: '#020A33',
          800: '#0C1A66',
          700: '#1D2F99',
          600: '#354ACB',
          500: '#556AEB',
          400: '#6E82FE',
          300: '#8FA0FF',
          200: '#B9C4FF',
          100: '#EBEFFF',
        },
        neutral: {
          900: '#212529',
          800: '#343A40',
          700: '#495057',
          600: '#6C757D',
          500: '#ADB5BD',
          400: '#CED4DA',
          300: '#DEE2E6',
          200: '#E9ECEF',
          100: '#F8F9FA',
        },
      },
    },
    keyframes: {
      shimmer: {
        '100%': {
          transform: 'translateX(100%)',
        },
      },
    },
  },
  plugins: [formKitTailwind, require('@tailwindcss/line-clamp')],
};
