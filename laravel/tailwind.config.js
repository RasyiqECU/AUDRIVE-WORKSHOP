/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/**/*.js',
        './resources/js/**/*.js',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            colors: {
                nero: '#0A0A0B',
                carbon: '#101114',
                steel: '#1B1D22',
                gunmetal: '#24272E',
                silver: {
                    DEFAULT: '#C7CCD1',
                    dim: '#6B7280',
                    bright: '#E5E7EB',
                },
                giallo: {
                    DEFAULT: '#FFD100',
                    light: '#FFE14D',
                    dark: '#CCa800',
                },
                arancio: {
                    DEFAULT: '#FF6A00',
                    light: '#FF8A33',
                    dark: '#CC5500',
                },
                verde: {
                    DEFAULT: '#7ACB00',
                    light: '#95E61A',
                    dark: '#61A200',
                },
                danger: {
                    DEFAULT: '#E10600',
                    light: '#FF2A24',
                    dark: '#B30500',
                },
            },

            fontFamily: {
                display: ['Orbitron', 'sans-serif'],
                heading: ['Rajdhani', 'sans-serif'],
                body: ['Inter', 'sans-serif'],
                mono: ['JetBrains Mono', 'monospace'],
            },

            backgroundImage: {
                'carbon-fiber': `
                    repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,0.015) 2px, rgba(255,255,255,0.015) 4px),
                    repeating-linear-gradient(90deg, transparent, transparent 2px, rgba(255,255,255,0.015) 2px, rgba(255,255,255,0.015) 4px)
                `,
                'brushed-metal': `
                    repeating-linear-gradient(90deg, transparent, transparent 1px, rgba(199,204,209,0.03) 1px, rgba(199,204,209,0.03) 2px)
                `,
                'blueprint-grid': `
                    linear-gradient(rgba(255,209,0,0.05) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,209,0,0.05) 1px, transparent 1px),
                    linear-gradient(rgba(255,209,0,0.02) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,209,0,0.02) 1px, transparent 1px)
                `,
                'hazard-stripes': `
                    repeating-linear-gradient(-45deg, #FFD100, #FFD100 10px, #0A0A0B 10px, #0A0A0B 20px)
                `,
                'speed-lines': `
                    linear-gradient(90deg, transparent 0%, rgba(255,209,0,0.03) 50%, transparent 100%),
                    repeating-linear-gradient(90deg, transparent, transparent 40px, rgba(255,209,0,0.02) 40px, rgba(255,209,0,0.02) 41px)
                `,
            },

            backgroundSize: {
                'blueprint-grid': '100px 100px, 100px 100px, 20px 20px, 20px 20px',
            },

            boxShadow: {
                'panel': '0 4px 24px rgba(0,0,0,0.5), 0 1px 4px rgba(0,0,0,0.3)',
                'glow-yellow': '0 0 20px rgba(255,209,0,0.3), 0 0 60px rgba(255,209,0,0.1)',
                'glow-orange': '0 0 20px rgba(255,106,0,0.3), 0 0 60px rgba(255,106,0,0.1)',
                'glow-verde': '0 0 20px rgba(122,203,0,0.3), 0 0 60px rgba(122,203,0,0.1)',
                'glow-danger': '0 0 20px rgba(225,6,0,0.3), 0 0 60px rgba(225,6,0,0.1)',
            },

            clipPath: {
                'hexagon': 'polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%)',
                'angular-card': 'polygon(0 0, calc(100% - 16px) 0, 100% 16px, 100% 100%, 16px 100%, 0 calc(100% - 16px))',
                'angular-button': 'polygon(0 0, calc(100% - 8px) 0, 100% 8px, 100% 100%, 8px 100%, 0 calc(100% - 8px))',
            },

            borderRadius: {
                'angular': '2px',
                'chamfer': '8px',
            },

            letterSpacing: {
                'ultra-wide': '0.3em',
            },

            animation: {
                'pulse-glow': 'pulse-glow 2s ease-in-out infinite',
                'slide-in': 'slide-in-right 0.4s ease-out forwards',
                'scan-line': 'scan-line 3s linear infinite',
            },

            keyframes: {
                'pulse-glow': {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.6' },
                },
                'slide-in-right': {
                    '0%': { transform: 'translateX(20px)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
                'scan-line': {
                    '0%': { transform: 'translateY(-100%)' },
                    '100%': { transform: 'translateY(100vh)' },
                },
            },

            transitionTimingFunction: {
                'premium': 'cubic-bezier(0.4, 0, 0.2, 1)',
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};