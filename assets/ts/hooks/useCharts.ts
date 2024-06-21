import { useState, useEffect } from 'react';
import axios from 'axios';
import { APIEndpoint } from '../utils/endpoints/api';

export function useCharts(sprintId: string | number) {
    const [fetchTrigger, setFetchTrigger] = useState<number>(0);
    const [isDataFetched, setDataFetch] = useState(false);

    const endpoints = [
        APIEndpoint.sprintData.replace('{sprintId}', sprintId.toString()),
        APIEndpoint.perfectChartLine.replace('{sprintId}', sprintId.toString()),
        APIEndpoint.currentChartLine.replace('{sprintId}', sprintId.toString()),
        APIEndpoint.chartLines.replace('{sprintId}', sprintId.toString())
    ];
    const [chartLineData, setChartLineData] = useState<(any | null)[]>(endpoints.map(() => null));
    const [errors, setErrors] = useState<(any | null)[]>(endpoints.map(() => null));


    useEffect(() => {
        const fetchData = async () => {
            try {
                const responses = await axios.all(endpoints.map((endpoint) => axios.post(endpoint)));
                const data = responses.map(response => response.data);
                setChartLineData(data);
                setDataFetch(!!data)
            } catch (error) {
                setErrors(error);
            }
        };
        fetchData();
    }, [fetchTrigger]);

    return {
        chartLineData,
        errors,
        setFetchTrigger,
        fetchTrigger,
        isDataFetched,
        setDataFetch
    };
}
