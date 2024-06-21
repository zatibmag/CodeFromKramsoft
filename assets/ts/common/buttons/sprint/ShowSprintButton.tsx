import * as React from 'react';
import { useContext } from 'react';
import { Button, ButtonType } from '../Button';
import { PageManagerContext, Pages } from '../../../react/context/PageManagerProvider';
import { useLoadingManager } from '../../../react/context/LoadingManager';

interface ShowButtonProps {
    value: number | string,
    handleSelectedSprint: (sprintId: number | string) => void
    isDisabled: boolean
}

export function ShowSprintButton({value, handleSelectedSprint, isDisabled}: ShowButtonProps) {
    const { setCurrentPage } = useContext(PageManagerContext);
    const { setLoading } = useLoadingManager();

    const handleShow = (button: HTMLButtonElement) => {
        handleSelectedSprint(button.value);
        setCurrentPage(Pages.SprintView);
        setLoading(true);
    };

    return <Button
        className={'btn btn-primary mt-1 mb-1'}
        type={ButtonType.Button}
        text={'Show'}
        value={value}
        onClick={(e) => handleShow(e.target as HTMLButtonElement)}
        isDisabled={isDisabled}
    />;
}
