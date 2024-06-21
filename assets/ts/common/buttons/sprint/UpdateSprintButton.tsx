import * as React from 'react';
import { Button, ButtonType } from '../Button';
import { useLoadingManager } from '../../../react/context/LoadingManager';

interface UpdateSprintButtonProps {
    isDisabled: boolean;
}

export function UpdateSprintButton({isDisabled}: UpdateSprintButtonProps) {
    const { setLoading } = useLoadingManager();

    return <Button
        className={'btn btn-block w-100 btn-primary'}
        type={ButtonType.Submit}
        text={'Update Sprint'}
        isDisabled={isDisabled}
        onClick={() => {
            setLoading(true)
        }}
    />;
}
