import * as React from 'react';
import { Button, ButtonType } from './Button';
import { useContext } from 'react';
import { PageManagerContext, Pages } from '../../react/context/PageManagerProvider';
import { useLoadingManager } from '../../react/context/LoadingManager';

interface BackToHomeButtonProps {
    styles: string,
    handleSelectedSprint: (sprintId: number | string) => void,
}

export function BackToHomeButton({styles, handleSelectedSprint}: BackToHomeButtonProps) {
    const { setCurrentPage } = useContext(PageManagerContext);
    const { setLoading } = useLoadingManager();

    const handleReturn = (button: HTMLButtonElement) => {
        setCurrentPage(Pages.Home);
        setLoading(true);
        handleSelectedSprint(button.value);
    };

    return <div className={styles}>
        <Button
            className={'btn btn-block btn-warning'}
            type={ButtonType.Button}
            text={'Back to Home'}
            value={null}
            onClick={(e) => handleReturn(e.target as HTMLButtonElement)}
        />
    </div>;
}
