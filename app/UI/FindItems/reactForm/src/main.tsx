import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import App from './App.tsx';
import ContextProvider from './context/ContextProvider';
import './common.css';

createRoot(document.getElementById('search_react')!).render(
    <StrictMode>
        <ContextProvider>
            <App />
        </ContextProvider>
    </StrictMode>,
);
