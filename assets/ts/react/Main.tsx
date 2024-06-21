import * as React from 'react';
import { useState, useEffect, useContext } from 'react';
import { useSprints } from '../hooks/useSprints';
import { HomePage } from './pages/HomePage';
import { SprintViewPage } from './pages/SprintViewPage';
import { SettingsUsersPage } from './pages/SettingsUsersPage';
import { Navbar } from './components/Navbar';
import { PageManagerContext, Pages } from './context/PageManagerProvider';

export function Main(): React.JSX.Element {
    const [sprints, setSprints] = useState([]);
    const [selectedSprintId, setSelectedSprintId] = useState(null);
    const [limit, setLimit] = useState(5);
    const [offset, setOffset] = useState(0);
    const [isLoading, setIsLoading] = useState(false);
    const [disableLoadMore, setDisableLoadMore] = useState(false);

    const {
        sprintsData,
        setFetchTrigger,
        fetchTrigger,
        isDataFetched,
        setDataFetch,
        setFetchAll,
        sprintsNumber,
    } = useSprints(selectedSprintId, limit, offset);

    const handleSelectedSprint = (sprintId: string | number) => {
        setSelectedSprintId(sprintId);
    };

    const handleSettingLimit = (limit: number) => {
        setDataFetch(false);
        setSprints([]);
        setOffset(0);
        setLimit(limit);
        setDisableLoadMore(false);
        setFetchAll(true);
        setFetchTrigger((prevFetchTrigger) => prevFetchTrigger + 1);
    };

    const handleLoadMore = () => {
        setDataFetch(false);
        setOffset(sprints.length);
        setIsLoading(true);
        setFetchTrigger((prevFetchTrigger) => prevFetchTrigger + 1);
    };

    useEffect(() => {
        if (isDataFetched) {
            if (sprintsData.length === 0 || sprints.length % 5 !== 0) {
                setDisableLoadMore(true);
            }
            setSprints((prevSprints) => [...prevSprints, ...sprintsData]);
            setIsLoading(false);
        }
    }, [isDataFetched, fetchTrigger]);

    const homeProps = {
        sprints:              sprints,
        handleSelectedSprint: handleSelectedSprint,
        handleLoadMore:       handleLoadMore,
        isLoading,
        disableLoadMore,
        handleSettingLimit:   handleSettingLimit,
        sprintsNumber:        sprintsNumber,
    };

    const sprintViewProps = {
        sprints:              sprints,
        handleSettingLimit:   handleSettingLimit,
        selectedSprintId:     selectedSprintId,
        handleSelectedSprint: handleSelectedSprint,
    };

    const {currentPage} = useContext(PageManagerContext);

    return (
        <div className="vh-100 d-flex flex-column">
            <Navbar />
            {currentPage === Pages.Settings && <SettingsUsersPage />}
            {currentPage === Pages.SprintView && <SprintViewPage {...sprintViewProps} />}
            {currentPage === Pages.Home && <HomePage {...homeProps} />}
        </div>
    );
}
