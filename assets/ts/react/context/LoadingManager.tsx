import * as React from 'react';
import { createContext, useContext, useState } from 'react';

interface LoadingManagerContextType {
    loading: boolean;
    setLoading: React.Dispatch<React.SetStateAction<boolean>>;
}

export const LoadingManagerContext = createContext<LoadingManagerContextType>({
    loading:    true,
    setLoading: () => {
    }
});

export const LoadingManager: React.FC = ({children}) => {
    const [loading, setLoading] = useState(true);

    return (
        <LoadingManagerContext.Provider value={{loading, setLoading}}>
            {children}
        </LoadingManagerContext.Provider>
    );
};

export const useLoadingManager = () => useContext(LoadingManagerContext);
