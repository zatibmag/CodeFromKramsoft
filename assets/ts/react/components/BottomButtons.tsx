import * as React from 'react';
import { BackToHomeButton } from '../../common/buttons/BackToHomeButton';
import { SaveButton } from '../../common/buttons/SaveButton';
import { APIEndpoint } from '../../utils/endpoints/api';
import axios from 'axios';

interface BottomButtonsProps {
    selectedRoles: {[key: number]: string};
    setSelectedRoles: React.Dispatch<React.SetStateAction<{[key: number]: string}>>;
}

export function BottomButtons({
    selectedRoles,
    setSelectedRoles
}: BottomButtonsProps) {

    const handleSubmit = async () => {
        try {
            const promises = Object.keys(selectedRoles).map(async userId => {
                const selectedRole = selectedRoles[parseInt(userId)];
                const apiUpdateUrl = `${APIEndpoint.settingsChangeRole.replace('{userId}', userId.toString())}`;
                await axios.post(apiUpdateUrl, null, {
                    params: {role: selectedRole}
                });
            });
            await Promise.all(promises);
            setSelectedRoles({});
        } catch (error) {
            console.error('Error changing roles:', error.message);
        }
    };

    return (
        <div className="d-flex justify-content-center p-3">
            <div className="p-2">
                <SaveButton styles={''} handleSubmit={handleSubmit} />
            </div>
            <div className="p-2">
                <BackToHomeButton
                    styles={''}
                    handleSelectedSprint={() => {
                    }}
                />
            </div>
        </div>
    );
}
