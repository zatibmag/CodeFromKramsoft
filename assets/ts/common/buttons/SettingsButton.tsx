import * as React from 'react';
import { Button, ButtonType } from './Button';
import { useContext } from 'react';
import { PageManagerContext, Pages } from '../../react/context/PageManagerProvider';
import { useLoadingManager } from '../../react/context/LoadingManager';

export function SettingsButton() {
    const { setCurrentPage } = useContext(PageManagerContext);
    const { setLoading } = useLoadingManager();

    const handleSettings = () => {
        setCurrentPage(Pages.Settings);
        setLoading(true);
    };

    return <Button
        className="btn btn-info"
        onClick={() => handleSettings()}
        type={ButtonType.Button}
        text={'Settings'}
    />;
}
