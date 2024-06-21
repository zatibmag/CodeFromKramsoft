import * as React from 'react';
import { useEffect, useState } from 'react';
import { BackToHomeButton } from '../../common/buttons/BackToHomeButton';
import { BottomButtons } from '../components/BottomButtons';
import { SettingsUsersList } from '../components/SettingsUsersList';
import { useUsers } from '../../hooks/useUsers';
import { useAvailableRoles } from '../../hooks/useAvailableRoles';
import { SpinnerWrapper } from '../components/SpinnerWrapper';
import { useLoadingManager } from '../context/LoadingManager';

export function SettingsUsersPage() {
    const [selectedRoles, setSelectedRoles] = useState<{[key: number]: string}>({});
    const { users } = useUsers();
    const { availableRoles } = useAvailableRoles();

    const handleChange = (userId: number, role: string) => {
        setSelectedRoles(prevState => ({
            ...prevState,
            [userId]: role
        }));
    };

    const BottomButtonsProps = {
        selectedRoles:    selectedRoles,
        setSelectedRoles: setSelectedRoles,
    };

    const SettingsUsersListProps = {
        handleChange:   handleChange,
        selectedRoles:  selectedRoles,
        users:          users,
        availableRoles: availableRoles,
    };

    const { loading, setLoading } = useLoadingManager();

    useEffect(() => {
        if (Object.keys(availableRoles).length > 0) {
            setLoading(false);
        }
    }, [availableRoles, setLoading]);

    const SettingsUserSection = () =>
        <div className="container d-flex flex-column justify-content-center p-3">
            <div className="bg-light rounded d-flex flex-column">
                <h4 className="p-2">Settings</h4>
                <div className="justify-content-center d-flex m-4">
                    <SettingsUsersList {...SettingsUsersListProps} />
                </div>
            </div>
            <BottomButtons {...BottomButtonsProps} />
        </div>;

    const SettingsNotUserSection = () =>
        <div className="container d-flex justify-content-center align-items-center vh-100">
            <div className="text-center">
                <p>There are no users</p>
                <BackToHomeButton
                    styles={''}
                    handleSelectedSprint={() => {
                    }}
                />
            </div>
        </div>;

    return <SpinnerWrapper isLoading={loading}>
        {users.length > 0
            ? SettingsUserSection()
            : SettingsNotUserSection()
        }
    </SpinnerWrapper>;
}
