import { PropsWithChildren } from 'react';
import { Head } from '@inertiajs/react';

export default function Guest({ children }: PropsWithChildren) {
    return (
        <>
            <Head>
                <link rel="preconnect" href="https://fonts.googleapis.com" />
                <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="anonymous" />
                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
                <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,300,0,0" />
            </Head>
            
            <div className="login-wrapper">
                <div className="floating-shape shape-1"></div>
                <div className="floating-shape shape-2"></div>
                
                {children}
            </div>
        </>
    );
}
