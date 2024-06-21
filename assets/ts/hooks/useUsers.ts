import { useState, useEffect } from 'react';
import axios from 'axios';
import { APIEndpoint } from '../utils/endpoints/api';

interface User {
    id: number;
    username: string;
    roles: string[];
}

export function useUsers() {
    const [error, setError] = useState(null);
    const [users, setUsers] = useState<User[]>([]);

    useEffect(() => {
        const fetchUsers = async () => {
            try {
                const response = await axios.post<User[]>(APIEndpoint.settingsUsers);
                setUsers(response.data);
            } catch (error) {
                console.error('Error fetching users:', error.message);
            }
        };

        fetchUsers();
    }, []);

    return {users, error};
}
