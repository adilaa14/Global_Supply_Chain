const fs = require('fs');

const html = fs.readFileSync('landingpage.html', 'utf8');

// Extract style
const styleMatch = html.match(/<style>([\s\S]*?)<\/style>/);
const style = styleMatch ? styleMatch[1] : '';

// Extract script
const scriptMatch = html.match(/<script>([\s\S]*?)<\/script>/);
const script = scriptMatch ? scriptMatch[1] : '';

// Extract body (excluding scripts and styles)
let body = html;
body = body.replace(/<style>[\s\S]*?<\/style>/, '');
body = body.replace(/<script>[\s\S]*?<\/script>/, '');
const bodyMatch = body.match(/<body>([\s\S]*?)<\/body>/);
body = bodyMatch ? bodyMatch[1] : body;

// Convert class to className
body = body.replace(/class=/g, 'className=');

// Convert inline styles
body = body.replace(/style="([^"]*)"/g, (match, p1) => {
    const obj = {};
    p1.split(';').forEach(pair => {
        if (!pair.trim()) return;
        let [key, ...values] = pair.split(':');
        let value = values.join(':');
        if (key && value) {
            const camelKey = key.trim().replace(/-([a-z])/g, g => g[1].toUpperCase());
            obj[camelKey] = value.trim();
        }
    });
    return `style={${JSON.stringify(obj)}}`;
});

// Convert HTML comments to JS comments in JSX
body = body.replace(/<!--([\s\S]*?)-->/g, '{/* $1 */}');

// The vanilla JS manipulates innerHTML. Instead of converting to React state,
// we will just wrap the HTML in a container and execute the script in useEffect.
// However, React throws warnings if we mix dangerouslySetInnerHTML and React children.
// The easiest way to port a vanilla HTML page with vanilla JS is to render it in dangerouslySetInnerHTML,
// and then use eval() or a script function to execute the logic once it mounts.

const reactCode = `import { PageProps } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { useEffect, useRef } from 'react';

export default function Welcome({ auth }: PageProps) {
    const containerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (!containerRef.current) return;
        
        // Execute the vanilla script logic
        ${script}
        
        // Add auth links dynamically to the header
        const authContainer = document.createElement('div');
        authContainer.className = "flex items-center gap-3";
        authContainer.innerHTML = auth.user 
            ? '<button id="btn-dashboard" class="btn-primary text-sm py-2.5 px-5">Go to Dashboard <i class="fas fa-arrow-right text-xs"></i></button>'
            : '<button id="btn-login" class="hidden sm:inline text-sm font-medium" style="color:var(--dark);">Sign In</button><button id="btn-register" class="btn-primary text-sm py-2.5 px-5">Get Started <i class="fas fa-arrow-right text-xs"></i></button>';
            
        const headerRight = containerRef.current.querySelector('header > div > div:last-child');
        if (headerRight) {
            headerRight.replaceWith(authContainer);
        }

        const btnDashboard = containerRef.current.querySelector('#btn-dashboard');
        const btnLogin = containerRef.current.querySelector('#btn-login');
        const btnRegister = containerRef.current.querySelector('#btn-register');

        if (btnDashboard) btnDashboard.addEventListener('click', () => router.visit('/dashboard'));
        if (btnLogin) btnLogin.addEventListener('click', () => router.visit('/login'));
        if (btnRegister) btnRegister.addEventListener('click', () => router.visit('/register'));

    }, []);

    return (
        <>
            <Head title="TradeOptix — AI-Powered Global Trade Intelligence">
                <style>{${JSON.stringify(style)}}</style>
            </Head>
            <div ref={containerRef} dangerouslySetInnerHTML={{ __html: ${JSON.stringify(body.replace(/className=/g, 'class=').replace(/style={.*?}/g, match => {
                // dangerouslySetInnerHTML expects normal HTML, so revert the React conversions for this specific path!
                return match; // Actually it's easier to use the original raw HTML.
            }))} }} />
        </>
    );
}
`;

const originalRawBodyMatch = html.match(/<body>([\s\S]*?)<script>/);
const originalRawBody = originalRawBodyMatch ? originalRawBodyMatch[1] : '';

const finalReactCode = `import { PageProps } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { useEffect, useRef } from 'react';

export default function Welcome({ auth }: PageProps) {
    const containerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (!containerRef.current) return;
        
        // Execute the vanilla script logic
        ${script}
        
        // Add auth links dynamically to the header
        const authContainer = document.createElement('div');
        authContainer.className = "flex items-center gap-3";
        authContainer.innerHTML = auth.user 
            ? '<button id="btn-dashboard" class="btn-primary text-sm py-2.5 px-5">Go to Dashboard <i class="fas fa-arrow-right text-xs"></i></button>'
            : '<button id="btn-login" class="hidden sm:inline text-sm font-medium" style="color:var(--dark);">Sign In</button><button id="btn-register" class="btn-primary text-sm py-2.5 px-5">Get Started <i class="fas fa-arrow-right text-xs"></i></button>';
            
        const headerRight = containerRef.current.querySelector('header > div > div:last-child');
        if (headerRight) {
            headerRight.replaceWith(authContainer);
        }

        const btnDashboard = containerRef.current.querySelector('#btn-dashboard');
        const btnLogin = containerRef.current.querySelector('#btn-login');
        const btnRegister = containerRef.current.querySelector('#btn-register');

        if (btnDashboard) btnDashboard.addEventListener('click', () => router.visit('/dashboard'));
        if (btnLogin) btnLogin.addEventListener('click', () => router.visit('/login'));
        if (btnRegister) btnRegister.addEventListener('click', () => router.visit('/register'));

    }, []);

    return (
        <>
            <Head title="TradeOptix — AI-Powered Global Trade Intelligence">
                <style>{${JSON.stringify(style)}}</style>
            </Head>
            <div ref={containerRef} dangerouslySetInnerHTML={{ __html: ${JSON.stringify(originalRawBody)} }} />
        </>
    );
}
`;

fs.writeFileSync('resources/js/Pages/Welcome.tsx', finalReactCode);
