const fs = require('fs');
let content = fs.readFileSync('resources/js/Pages/Welcome.tsx', 'utf8');

// 1. Remove from navItems
content = content.replace(/\s*\{\s*label:\s*"Pricing",\s*href:\s*"#pricing"\s*\},?/, '');

// 2. Remove pricingPlans array
content = content.replace(/\s*const pricingPlans = \[[\s\S]*?\];/, '');

// 3. Remove renderPricing function
content = content.replace(/\s*function renderPricing\([\s\S]*?return items\.map[\s\S]*?\}\)\.join\(""\);\s*\}/, '');

// 4. Remove safeSetInnerHTML for pricing-grid
content = content.replace(/\s*safeSetInnerHTML\("pricing-grid", renderPricing\(pricingPlans\)\);/, '');

// 5. Remove HTML block
content = content.replace(/\s*<!-- PRICING -->[\s\S]*?<!-- FAQ -->/, '\n\n    <!-- FAQ -->');

// 6. Remove CSS block
content = content.replace(/\s*\.pricing-card \{[\s\S]*?\.faq-item \{/, '\n        .faq-item {');

fs.writeFileSync('resources/js/Pages/Welcome.tsx', content);
