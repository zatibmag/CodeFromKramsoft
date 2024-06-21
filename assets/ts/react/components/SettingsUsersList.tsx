import * as React from 'react';
import { SettingsRoleChanger } from './SettingsRoleChanger';
import { InputOrSpanWrapper } from '../../common/inputs/InputOrSpanWrapper';
import { useAuth, UserRole } from '../../hooks/useAuth';

interface SettingsUsersListProps {
    handleChange: (userId: number, role: string) => void;
    selectedRoles: {[key: number]: string};
    users: {id: number; username: string; roles: string[]}[];
    availableRoles: {[key: number]: string};
}

export function SettingsUsersList({
    handleChange,
    selectedRoles,
    users,
    availableRoles,
}: SettingsUsersListProps) {
    const { isSuperAdmin } = useAuth();

    let filteredAvailableRoles = availableRoles;

    if (!isSuperAdmin()) {
        filteredAvailableRoles = Object.fromEntries(
            Object.entries(filteredAvailableRoles).filter(([roleId, roleName]) => roleName !== UserRole.SuperAdmin)
        );
    }

    return <div className="border border-secondary rounded bg-gradient p-2 col-md-8">
        <h2>Change users role</h2>
        <div className="row justify-content-center">
            <div className="container">
                {users.map(user => (
                    <div key={user.id} className="d-flex flex-wrap border border-secondary rounded p-1 mt-2">
                        <div className="col-12 col-md-4">
                            <InputOrSpanWrapper hasPermission={false} value={user.username} label="Username" id="name">
                                {user.username}
                            </InputOrSpanWrapper>
                        </div>
                        <div className="col-12 col-md-8">
                            <SettingsRoleChanger
                                user={user}
                                handleChange={handleChange}
                                selectedRoles={selectedRoles}
                                availableRoles={filteredAvailableRoles}
                                disabled={user.roles[0] == UserRole.Admin && !isSuperAdmin()}
                            />
                        </div>
                    </div>
                ))}
            </div>
        </div>
    </div>;
}
