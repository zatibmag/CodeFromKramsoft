import * as React from 'react';
import { createContext, useContext, useEffect, useState } from 'react';

export enum Pages {
    Settings = 'Settings',
    SprintView = 'SprintView',
    Home = 'Home',
}

interface PageManagerContextType {
    currentPage: Pages | null;
    setCurrentPage: React.Dispatch<React.SetStateAction<Pages | null>>;
}

export const PageManagerContext = createContext<PageManagerContextType>({
    currentPage: null,
    setCurrentPage: () => {}
});

export const PageManagerProvider: React.FC = ({ children }) => {
    const [currentPage, setCurrentPage] = useState<Pages | null>(Pages.Home);

    useEffect(() => {
        if (!currentPage) {
            setCurrentPage(Pages.Home);
        }
    }, []);

    return (
        <PageManagerContext.Provider value={{ currentPage, setCurrentPage }}>
            {children}
        </PageManagerContext.Provider>
    );
};
