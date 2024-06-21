import { useState, useEffect } from 'react';
import axios from 'axios';

export function useBoardLists() {
    const [isStatusFetched, setStatusFetched] = useState(false);
    const [boardLists, setBoardLists] = useState([]);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get(`/lists`);
                setBoardLists(response.data);
                setStatusFetched(!!response);
            } catch (error) {
                setError(error);
            }
        };
        fetchData();
    }, []);

    return {
        boardLists,
        isStatusFetched,
        setStatusFetched,
        error
    };
}
