import * as React from 'react';
import { SprintList } from '../components/SprintList';
import { CreateNewSprintButton } from '../../common/buttons/sprint/CreateNewSprintButton';
import { useEffect, useState } from 'react';
import { ISprint } from '../../interfaces';
import { ButtonPermissionWrapper } from '../../common/buttons/ButtonPermissionWrapper';
import { useAuth } from '../../hooks/useAuth';
import { useLoadingManager } from '../context/LoadingManager';
import { SpinnerWrapper } from '../components/SpinnerWrapper';

interface HomeProps {
    sprints: ISprint[];
    handleSelectedSprint: (sprintId: string | number) => void;
    handleSettingLimit: (limit: number) => void;
    handleLoadMore: () => void;
    isLoading: boolean;
    disableLoadMore: boolean;
    sprintsNumber: number;
}

export function HomePage({
    sprints,
    handleSelectedSprint,
    handleLoadMore,
    handleSettingLimit,
    sprintsNumber
}: HomeProps) {

    const [areHomeButtonsDisabled, setHomeButtonsDisable] = useState(false);

    const handleHomeButtonsDisable = () => {
        setHomeButtonsDisable(true);
    };

    const { isAdmin, isScrumMaster, isSuperAdmin, permissionLevel } = useAuth();

    useEffect(() => {
        setHomeButtonsDisable(false);
    }, [sprints]);

    const { loading, setLoading } = useLoadingManager();

    useEffect(() => {
        !!permissionLevel && setLoading(false);
    }, [permissionLevel, setLoading]);

    return <SpinnerWrapper isLoading={loading}>
        <div className="vh-100 container d-flex flex-column justify-content-center">
            <div className="d-flex flex-column mx-auto p-2">
                <ButtonPermissionWrapper hasPermission={isAdmin() || isScrumMaster() || isSuperAdmin()}>
                    <CreateNewSprintButton handleSelectedSprint={handleSelectedSprint} additionalStyle={'mb-2'} />
                </ButtonPermissionWrapper>
                {sprints.length > 0 &&
                    <div className="card d-flex flex-row bg-secondary-subtle border-0 p-1">
                        <p className="my-auto">Loaded sprints&nbsp;</p>
                        <p className="my-auto card border-0 p-1">{sprints.length} / {sprintsNumber}</p>
                    </div>
                }
            </div>
            <SprintList
                sprints={sprints}
                hasPermission={isAdmin() || isScrumMaster() || isSuperAdmin()}
                handleSelectedSprint={handleSelectedSprint}
                handleSettingLimit={handleSettingLimit}
                areHomeButtonsDisabled={areHomeButtonsDisabled}
                handleHomeButtonsDisable={handleHomeButtonsDisable}
                handleLoadMore={handleLoadMore}
                sprintsNumber={sprintsNumber}
            />
        </div>
    </SpinnerWrapper>;
}
