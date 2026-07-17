const fs = require('fs');
const content = fs.readFileSync('resources/js/Pages/Welcome.tsx', 'utf8');
const lines = content.split('\n');

const html = fs.readFileSync('landingpage.html', 'utf8');
const styleMatch = html.match(/<style>([\s\S]*?)<\/style>/);
const style = styleMatch ? styleMatch[1] : '';

const bodyMatch = html.match(/<body>([\s\S]*?)<script>/);
let originalRawBody = bodyMatch ? bodyMatch[1] : '';

originalRawBody = originalRawBody.replace(/TradeOptix/g, 'Global Chain');

const fixedEnd = `
    return (
        <>
            <Head title="Global Chain — AI-Powered Global Trade Intelligence">
                <style>{\`${style}\`}</style>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
            </Head>
            <div ref={containerRef} dangerouslySetInnerHTML={{ __html: ${JSON.stringify(originalRawBody)} }} />
        </>
    );
}
`;

lines.splice(313); // lines are 0-indexed, so we keep 0 to 312
fs.writeFileSync('resources/js/Pages/Welcome.tsx', lines.join('\n') + fixedEnd);
