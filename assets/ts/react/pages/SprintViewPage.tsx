import * as React from 'react';
import { SprintForm } from '../components/SprintForm';
import { SprintChart } from '../components/SprintChart';
import { useEffect, useState } from 'react';
import { ChartData, ChartLine, ChartOptions, ChartPoint, IList, ISprint } from '../../interfaces';
import { CreateNewSprintButton } from '../../common/buttons/sprint/CreateNewSprintButton';
import { BackToHomeButton } from '../../common/buttons/BackToHomeButton';
import { ButtonPermissionWrapper } from '../../common/buttons/ButtonPermissionWrapper';
import { useCharts } from '../../hooks/useCharts';
import { useBoardLists } from '../../hooks/useBoardLists';
import { useAuth } from '../../hooks/useAuth';
import { SpinnerWrapper } from '../components/SpinnerWrapper';
import { useLoadingManager } from '../context/LoadingManager';
import { prepareDatasets } from '../../utils/prepareDatasets';

interface SprintViewProps {
    sprints: ISprint[];
    selectedSprintId: string | number;
    handleSelectedSprint: (sprintId: string | number) => void;
    handleSettingLimit: (limit: number) => void;
}

interface RenderChartProps {
    selectedSprintId: string | number;
    chartProps: {
        options: ChartOptions;
        data: ChartData;
        sprint: ISprint,
        lists: IList[],
        selectedList: string | null
        handleSettingTriggerChart: () => void
        handleSelectedSprint: (sprintId: number | string) => void
        handleButtonsDisable: () => void
        areButtonsDisabled: boolean
        hasPermission: boolean
        additionalChartLinesData:  ChartLine[]
    };
}

export function SprintViewPage({
    sprints,
    selectedSprintId,
    handleSelectedSprint,
    handleSettingLimit,
}: SprintViewProps) {
    const {boardLists, isStatusFetched} = useBoardLists();
    const {chartLineData, setFetchTrigger, isDataFetched, setDataFetch} = useCharts(selectedSprintId);
    const [sprintData, perfectChartLineData, currentChartLineData, additionalChartLinesData] = chartLineData;
    const [isChartLoading, setChartLoading] = useState(true);
    const [chartOptions, setChartOptions] = useState<ChartOptions | null>(null);
    const [chartData, setChartData] = useState<ChartData | null>(null);
    const [selectedList, setSelectedList] = useState<string | null>(null);
    const [areButtonsDisabled, setButtonDisable] = useState(false);
    const { isAdmin, isScrumMaster, isSuperAdmin } = useAuth();

    const handleSettingTriggerChart = () => {
        setDataFetch(false);
        setFetchTrigger((prev) => prev + 1);
    };

    const handleButtonsDisable = () => {
        setButtonDisable(true);
    };

    const sprintFormProps = {
        sprints:                   sprints,
        sprint:                    (isDataFetched && selectedSprintId !== 'newSprint') ? sprintData : null,
        lists:                     boardLists,
        selectedList:              sprintData ? sprintData.listDoneId : selectedList,
        setSelectedList:           setSelectedList,
        selectedSprintId:          selectedSprintId,
        handleSelectedSprint:      handleSelectedSprint,
        handleSettingLimit:        handleSettingLimit,
        handleSettingTriggerChart: handleSettingTriggerChart,
        handleButtonsDisable:      handleButtonsDisable,
        areButtonsDisabled:        areButtonsDisabled,
        hasPermission:             isAdmin() || isScrumMaster() || isSuperAdmin()
    };

    const chartProps = {
        options:                   chartOptions,
        data:                      chartData,
        sprint:                    isDataFetched ? sprintData : null,
        handleSelectedSprint:      handleSelectedSprint,
        handleButtonsDisable:      handleButtonsDisable,
        areButtonsDisabled:        areButtonsDisabled,
        selectedList:              selectedList,
        handleSettingTriggerChart: handleSettingTriggerChart,
        lists:                     boardLists,
        hasPermission:             isAdmin() || isScrumMaster() || isSuperAdmin(),
        additionalChartLinesData:  additionalChartLinesData
    };

    const RenderChart = ({selectedSprintId, chartProps}: RenderChartProps) => {
        if (selectedSprintId === 'newSprint') {
            return (
                <div className="col-xl-9 col-lg-7 col bg-light p-2 rounded h-100 d-flex flex-column justify-content-center">
                    <p className="text-center">The chart is not yet available. Please fill out the form and submit it to
                        generate data for the chart</p>
                </div>
            );
        }

        return !isChartLoading
            ? <SprintChart {...chartProps} />
            : <></>;
    };

    useEffect(() => {
        setChartLoading(true);
        if (isDataFetched) {

            const formattedLabel = Array.from(perfectChartLineData)
                .map((row: ChartPoint) => row.x)
                .filter((value, index, array) => array.indexOf(value) === index);

            setChartData({
                labels:   formattedLabel,
                datasets: prepareDatasets(currentChartLineData,perfectChartLineData,additionalChartLinesData)
            });

            setChartOptions({
                responsive: true,
                plugins:    {
                    legend: {
                        position: 'top' as const,
                    },
                    title:  {
                        display: true,
                        text:    sprintData.name,
                    },
                },
                scales:     {
                    y: {
                        beginAtZero: true,
                        min:         0
                    }
                }
            });
            setChartLoading(false);
            setButtonDisable(false);
        }
    }, [isDataFetched, selectedSprintId]);

    const { setLoading } = useLoadingManager();

    useEffect(() => {
        if ((selectedSprintId === 'newSprint' || isDataFetched) && isStatusFetched && !areButtonsDisabled) {
            setLoading(false);
        }
    }, [isAdmin(), isScrumMaster(), isDataFetched, isStatusFetched, areButtonsDisabled]);

    return (
        <SpinnerWrapper isLoading={!isDataFetched && selectedSprintId !== 'newSprint' || areButtonsDisabled || !isStatusFetched}>
            <div className="container vh-100 d-flex flex-column justify-content-center">
                {boardLists.length > 0 && !areButtonsDisabled && (isDataFetched || selectedSprintId === 'newSprint')
                    ? (
                        <div className="d-flex gap-3 flex-column flex-lg-row">
                            <RenderChart selectedSprintId={selectedSprintId} chartProps={chartProps} />
                            <div className="col-xl-3 col-lg-5 col bg-light p-2 rounded">
                                {selectedSprintId === 'newSprint' &&
                                    <SprintForm {...sprintFormProps} />
                                }
                                {isDataFetched && selectedSprintId !== 'newSprint' &&
                                    (
                                        <div className="h-100 d-flex flex-column justify-content-between">
                                            <SprintForm {...sprintFormProps} />
                                            <ButtonPermissionWrapper hasPermission={isAdmin() || isScrumMaster() || isSuperAdmin()}>
                                                <CreateNewSprintButton
                                                    handleSelectedSprint={handleSelectedSprint}
                                                    additionalStyle={'w-100'}
                                                />
                                            </ButtonPermissionWrapper>
                                        </div>
                                    )
                                }
                            </div>
                        </div>
                    )
                    : (
                        <div className="d-flex justify-content-center align-items-center">
                            <p>No lists found. Please add list to your project board.</p>
                        </div>
                    )
                }
            </div>
            <BackToHomeButton
                styles={'align-self-center mt-3'}
                handleSelectedSprint={handleSelectedSprint}
            />
        </SpinnerWrapper>
    );
}
