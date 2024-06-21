import * as React from 'react';
import { PageManagerProvider } from './context/PageManagerProvider';
import { LoadingManager } from './context/LoadingManager';
import { Main } from './Main';

export function App(): React.JSX.Element {
    return (
        <PageManagerProvider>
            <LoadingManager>
                <Main />
            </LoadingManager>
        </PageManagerProvider>
    );
}
