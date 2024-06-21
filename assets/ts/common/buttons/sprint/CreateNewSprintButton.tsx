import * as React from 'react';
import { Button, ButtonType } from '../Button';
import { useContext } from 'react';
import { PageManagerContext, Pages } from '../../../react/context/PageManagerProvider';
import { useLoadingManager } from '../../../react/context/LoadingManager';

interface CreateNewSprintButtonProps {
    handleSelectedSprint: (sprintId: number | string) => void;
    additionalStyle: string;
}

export function CreateNewSprintButton({
    handleSelectedSprint,
    additionalStyle
}: CreateNewSprintButtonProps) {
    const { currentPage, setCurrentPage } = useContext(PageManagerContext);
    const { setLoading } = useLoadingManager();

    const handleCreateNewSprint = (button: HTMLButtonElement) => {
        handleSelectedSprint(button.value);
        setCurrentPage(Pages.SprintView);
        currentPage == Pages.Home && setLoading(true);
    };

    return <div className={`${additionalStyle}`}>
        <Button
            className={'btn btn-block btn-success w-100 text-white'}
            type={ButtonType.Button}
            value={'newSprint'}
            text={'Create new Sprint'}
            onClick={(e) => handleCreateNewSprint(e.target as HTMLButtonElement)}
        />
    </div>;
}
