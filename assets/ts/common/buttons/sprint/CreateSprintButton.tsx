import * as React from 'react';
import { Button, ButtonType } from '../Button';
import { useLoadingManager } from '../../../react/context/LoadingManager';

interface CreateSprintButtonProps {
    isDisabled: boolean;
}

export function CreateSprintButton({isDisabled}: CreateSprintButtonProps) {
    const { setLoading } = useLoadingManager();

    return <Button
        className={'btn btn-block w-100 btn-success mb-1'}
        type={ButtonType.Submit}
        text={'Create Sprint'}
        isDisabled={isDisabled}
        onClick={() => {
            setLoading(true)
        }}
    />;
}
