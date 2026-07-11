/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './app/components/**/*.{vue,js,ts}',
    './app/layouts/**/*.vue',
    './app/pages/**/*.vue',
    './app/app.vue',
  ],
  theme: {
    extend: {
      colors: {
        background: 'hsl(var(--background, 40 33% 98%))',
        foreground: 'hsl(var(--foreground, 222 25% 11%))',
        card: {
          DEFAULT: 'hsl(0 0% 100%)',
          foreground: 'hsl(222 25% 11%)',
        },
        primary: {
          DEFAULT: 'hsl(38 64% 30%)',
          foreground: 'hsl(40 33% 98%)',
        },
        secondary: {
          DEFAULT: 'hsl(220 14% 96%)',
          foreground: 'hsl(222 25% 11%)',
        },
        muted: {
          DEFAULT: 'hsl(220 14% 94%)',
          foreground: 'hsl(220 9% 46%)',
        },
        accent: {
          DEFAULT: 'hsl(38 58% 92%)',
          foreground: 'hsl(38 64% 20%)',
        },
        destructive: {
          DEFAULT: 'hsl(0 72% 51%)',
          foreground: 'hsl(0 0% 98%)',
        },
        border: 'hsl(220 13% 88%)',
        ring: 'hsl(38 64% 30%)',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        serif: ['"Playfair Display"', 'Georgia', 'serif'],
      },
      borderRadius: {
        lg: '0.5rem',
        md: 'calc(0.5rem - 2px)',
        sm: 'calc(0.5rem - 4px)',
      },
    },
  },
  plugins: [],
};
