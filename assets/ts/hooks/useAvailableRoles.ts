import { useState, useEffect } from 'react';
import axios from 'axios';
import { APIEndpoint } from '../utils/endpoints/api';

interface AvailableRoles {
    [key: number]: string;
}

export function useAvailableRoles() {
    const [error, setError] = useState(null);

    const [availableRoles, setAvailableRoles] = useState<AvailableRoles>({});

    useEffect(() => {
        const fetchAvailableRoles = async () => {
            try {
                const response = await axios.post<AvailableRoles>(APIEndpoint.settingsAvailableRoles);
                setAvailableRoles(response.data);
            } catch (error) {
                console.error('Error fetching available roles:', error.message);
            }
        };

        fetchAvailableRoles();
    }, []);

    return {availableRoles, error};
}
