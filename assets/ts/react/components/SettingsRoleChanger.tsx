import * as React from 'react';
import { SelectOrSpanWrapper } from '../../common/inputs/SelectOrSpanWrapper';
import { UserRole } from '../../hooks/useAuth';

interface RoleChangerProps {
    user: {id: number; username: string; roles: string[]};
    handleChange: (userId: number, role: string) => void;
    selectedRoles: {[key: number]: string};
    availableRoles: {[key: number]: string};
    disabled: boolean;
}

export function SettingsRoleChanger({
    user,
    handleChange,
    selectedRoles,
    availableRoles,
    disabled
}: RoleChangerProps) {
    return (
        <SelectOrSpanWrapper
            hasPermission={true}
            value={selectedRoles[user.id]}
            label={disabled ? "No permission" : "Role"}
            id={`role-${user.id}`}
            options={[]}
        >
            <select
                value={selectedRoles[user.id]}
                onChange={(e) => handleChange(user.id, e.target.value)}
                className="form-select form-floating mt-1 mb-1"
                disabled={disabled}
            >
                {user.roles.map((role, index) => (
                    <option key={index} value={role}>{role}</option>
                ))}
                {Object.entries(availableRoles).map(([roleId, roleName]) => (
                    !user.roles.includes(roleName) && (
                    <option key={roleId} value={roleName}>{roleName}</option>
                )
            ))}
            </select>
        </SelectOrSpanWrapper>
    );
}
