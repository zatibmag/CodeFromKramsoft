import * as React from 'react';
import { ISprint } from '../../interfaces';
import { SprintListItem } from './SprintListItem';
import { NoSprintsMessage } from './NoSprintsMessage';
import { LoadButton } from '../../common/buttons/LoadButton';

interface SprintsListProps {
    sprints: ISprint[];
    hasPermission: boolean;
    handleSelectedSprint: (sprintId: string | number) => void;
    handleSettingLimit: (limit: number) => void;
    areHomeButtonsDisabled: boolean;
    handleHomeButtonsDisable: () => void;
    handleLoadMore: () => void;
    sprintsNumber: number;
}

export function SprintList({
    sprints,
    hasPermission,
    handleSelectedSprint,
    handleSettingLimit,
    areHomeButtonsDisabled,
    handleHomeButtonsDisable,
    handleLoadMore,
    sprintsNumber
}: SprintsListProps) {
    if (!sprints) {
        return <></>;
    }

    return <div
        className="d-flex container mx-auto scrolling-wrapper row flex-wrap flex-lg-nowrap mt-2 gap-2
         overflow-scroll justify-content-center justify-content-lg-start"
    >
        {sprints.length === 0
            ? (<NoSprintsMessage />)
            : (
                <>
                    {sprints.map((sprint: ISprint) => (
                        <SprintListItem
                            key={sprint.id}
                            sprint={sprint}
                            sprints={sprints}
                            handleSelectedSprint={handleSelectedSprint}
                            hasPermission={hasPermission}
                            areHomeButtonsDisabled={areHomeButtonsDisabled}
                            handleHomeButtonsDisable={handleHomeButtonsDisable}
                            handleSettingLimit={handleSettingLimit}
                        />
                    ))} <LoadButton
                    sprints={sprints}
                    handleLoadMore={handleLoadMore}
                    handleButtonsDisable={handleHomeButtonsDisable}
                    areHomeButtonsDisabled={areHomeButtonsDisabled}
                    sprintsNumber={sprintsNumber}
                />
                </>
            )}
    </div>;
}
