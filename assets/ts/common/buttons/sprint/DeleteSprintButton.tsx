import * as React from 'react';
import { useContext } from 'react';
import axios from 'axios';
import { Button, ButtonType } from '../Button';
import { ISprint } from '../../../interfaces';
import { APIEndpoint } from '../../../utils/endpoints/api';
import { PageManagerContext, Pages } from '../../../react/context/PageManagerProvider';
import { useLoadingManager } from '../../../react/context/LoadingManager';

interface DeleteSprintButtonProps {
    sprints: ISprint[]
    selectedSprintId: number | string,
    handleSelectedSprint: (sprintId: number | string) => void
    handleSettingLimit: (limit: number) => void
    isDisabled: boolean
    handleButtonsDisable: () => void
}

export function DeleteSprintButton({
    sprints,
    selectedSprintId,
    handleSelectedSprint,
    handleSettingLimit,
    isDisabled,
    handleButtonsDisable,
}: DeleteSprintButtonProps) {
    const handleDelete = async (button: HTMLButtonElement) => {
        const apiUrlRemove = `${APIEndpoint.sprintRemove.replace('{sprintId}', selectedSprintId.toString())}`;
        try {
            axios.post(apiUrlRemove).then(() => {
                handleSettingLimit(sprints.length);
                handleSelectedSprint(button.value);
            });
        } catch (error) {
            console.error(error);
        }
    };

    const { currentPage, setCurrentPage } = useContext(PageManagerContext);
    const { setLoading } = useLoadingManager();

    return <Button
        className={'btn btn-block btn-danger w-100 mt-1 mb-1'}
        type={ButtonType.Button}
        text={'Delete Sprint'}
        value={null}
        onClick={(e) => {
            handleButtonsDisable();
            handleDelete(e.target as HTMLButtonElement);
            setCurrentPage(Pages.Home);
            currentPage == Pages.SprintView && setLoading(true);
        }}
        isDisabled={isDisabled}
    />;
}

