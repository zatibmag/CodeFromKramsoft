import { ShowSprintButton } from '../../common/buttons/sprint/ShowSprintButton';
import { DeleteSprintButton } from '../../common/buttons/sprint/DeleteSprintButton';
import * as React from 'react';
import { ISprint } from '../../interfaces';
import { ButtonPermissionWrapper } from '../../common/buttons/ButtonPermissionWrapper';

interface SprintProps {
    sprint: ISprint;
    sprints: ISprint[];
    hasPermission: boolean;
    handleSelectedSprint: (sprintId: string | number) => void;
    handleSettingLimit: (limit: number) => void;
    areHomeButtonsDisabled: boolean;
    handleHomeButtonsDisable: () => void;
}

export function SprintListItem({
    sprint,
    sprints,
    handleSelectedSprint,
    areHomeButtonsDisabled,
    handleSettingLimit,
    handleHomeButtonsDisable,
    hasPermission
}: SprintProps) {
    function formatDate(date: string) {
        return date.replace(/-/g, '.');
    }

    return <div className="card col-12 col-md-3 card-block p-2">
        <p className="text-center">{sprint.name}</p>
        <p className="text-center">{formatDate(sprint.startAt)} - {formatDate(sprint.endAt)}</p>
        <ShowSprintButton
            value={sprint.id}
            handleSelectedSprint={handleSelectedSprint}
            isDisabled={areHomeButtonsDisabled}
        />
        <ButtonPermissionWrapper hasPermission={hasPermission}>
            <DeleteSprintButton
                sprints={sprints}
                selectedSprintId={sprint.id}
                handleSelectedSprint={handleSelectedSprint}
                handleSettingLimit={handleSettingLimit}
                handleButtonsDisable={handleHomeButtonsDisable}
                isDisabled={areHomeButtonsDisabled} />
        </ButtonPermissionWrapper>
    </div>;
}
