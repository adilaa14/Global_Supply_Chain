const fs = require('fs');
let c = fs.readFileSync('resources/js/Pages/Welcome.tsx', 'utf8');

// The file currently has a syntax error because dangerouslySetInnerHTML={{ __html: "
// <header...
// " }} /> contains literal newlines inside a double quoted string.

// We need to find the start and end of this string and convert the double quotes to backticks.
// The start is: __html: "\n\n<header
// The end is: </footer>\n\n" }} />

c = c.replace(/__html: \"\n\n<header/, "__html: `\n\n<header");
c = c.replace(/<\/footer>\n\n\" \}\} \/>/, "</footer>\n\n` }} />");

fs.writeFileSync('resources/js/Pages/Welcome.tsx', c);
