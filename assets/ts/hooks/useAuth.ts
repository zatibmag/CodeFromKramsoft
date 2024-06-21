import { useState, useEffect } from 'react';
import axios from 'axios';
import { APIEndpoint } from '../utils/endpoints/api';

export enum UserRole {
    SuperAdmin = 'ROLE_SUPER_ADMIN',
    Admin = 'ROLE_ADMIN',
    ScrumMaster = 'ROLE_SCRUM_MASTER',
    TeamMember = 'ROLE_TEAM_MEMBER',
}

export function useAuth() {
    const [error] = useState(null);
    const [permissionLevel, setPermissionLevel] = useState(null);

    const isSuperAdmin = () => permissionLevel === UserRole.SuperAdmin;
    const isAdmin = () => permissionLevel === UserRole.Admin;
    const isScrumMaster = () => permissionLevel === UserRole.ScrumMaster;
    const isTeamMember = () => permissionLevel === UserRole.TeamMember;

    const fetchPermission = async () => {
        try {
            const response = await axios.post(`${APIEndpoint.getPermission}`);
            setPermissionLevel(response.data);
        } catch (error) {
            console.error(error);
        }
    };

    useEffect(() => {
        !permissionLevel && fetchPermission();
    }, []);

    return {permissionLevel, fetchPermission, error, isAdmin, isScrumMaster, isTeamMember, isSuperAdmin};
}
