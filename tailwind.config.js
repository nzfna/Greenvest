/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                // Admin: Forest Green palette
                forest: {
                    50:  '#E8F5E9',
                    100: '#D8F3DC',
                    700: '#40916C',
                    800: '#2D6A4F',
                    900: '#1B4332',
                },
                // Admin: Olive Gold CTA
                olive: {
                    50:  '#FDF8E8',
                    600: '#8B6B1B',
                    700: '#6B530F',
                },
                // User: Forest
                'user-forest': {
                    50:  '#E8F5E9',
                    800: '#1A4D3E',
                    900: '#0D3B2E',
                },
                // Shared neutrals
                cream: {
                    50: '#F9F7F2',
                },
                // Semantic
                success: {
                    bg:   '#D1E7DD',
                    text: '#0F5132',
                },
                warning: {
                    bg:   '#FFF3CD',
                    text: '#856404',
                },
                danger: {
                    bg:   '#F8D7DA',
                    text: '#842029',
                    600:  '#C82333',
                    700:  '#A71D2A',
                },
            },
            fontFamily: {
                sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
            },
            boxShadow: {
                'card': '0 4px 20px rgba(27, 67, 50, 0.06)',
                'modal': '0 8px 32px rgba(0, 0, 0, 0.12)',
                'auth': '0 20px 60px rgba(27, 67, 50, 0.12)',
                'fab': '0 4px 12px rgba(27, 67, 50, 0.15)',
            },
        },
    },
    plugins: [],
};
