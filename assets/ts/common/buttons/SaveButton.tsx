import * as React from 'react';
import { Button, ButtonType } from './Button';
import { useContext } from 'react';
import { PageManagerContext, PageManagerProvider, Pages } from '../../react/context/PageManagerProvider';
import { useLoadingManager } from '../../react/context/LoadingManager';

interface SaveButtonProps {
    styles: string,
    handleSubmit: () => void;
}

export function SaveButton({styles, handleSubmit}: SaveButtonProps) {
    const { setCurrentPage } = useContext(PageManagerContext);
    const { setLoading } = useLoadingManager();

    return <div className={styles}>
        <Button
            className={'btn btn-block btn-success'}
            type={ButtonType.Button}
            text={'Save'}
            value={null}
            onClick={() => {
                handleSubmit();
                setCurrentPage(Pages.Home);
                setLoading(true);
            }}
        />
    </div>;
}
