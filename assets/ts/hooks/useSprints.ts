import { useState, useEffect } from 'react';
import axios from 'axios';
import { APIEndpoint } from '../utils/endpoints/api';
import { ISprint } from '../interfaces';

export function useSprints(sprintId: number = null, limit: number, offset: number) {
    const [fetchTrigger, setFetchTrigger] = useState<number>(0);
    const [fetchAll, setFetchAll] = useState(false);
    const [isDataFetched, setDataFetch] = useState(false);
    const [sprintsData, setSprintsData] = useState<(ISprint | null)[]>(() => null);
    const [errors, setErrors] = useState<(any | null)[]>(() => null);
    const [sprintsNumber, setSprintsNumber] = useState(0);

    useEffect(() => {
        const fetchData = async () => {
            setDataFetch(false)
            try {
                let response;
                if (sprintId && !fetchAll) {
                    response = await axios.post(APIEndpoint.sprintData.replace('{sprintId}', sprintId.toString()));
                } else {
                    response = await axios.post(`${APIEndpoint.sprints}?limit=${limit}&offset=${offset}`);
                }
                setSprintsData(response.data.sprints);
                const { sprintsNumber } = response.data;
                setSprintsNumber(sprintsNumber);
                setDataFetch(!!response);
                setFetchAll(false);
            } catch (error) {
                setErrors(error);
            }
        };
        fetchData();
    }, [fetchTrigger]);
    return {
        sprintsData,
        errors,
        setFetchTrigger,
        fetchTrigger,
        isDataFetched,
        setDataFetch,
        setFetchAll,
        sprintsNumber
    };
}
