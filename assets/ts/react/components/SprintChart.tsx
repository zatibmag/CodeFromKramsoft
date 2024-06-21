import * as React from 'react';
import { useRef } from 'react';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js';
import { Line } from 'react-chartjs-2';
import { DownloadPdfButton } from '../../common/buttons/chart/DownloadPdfButton';
import { ChartData, ChartLine, ChartOptions, ISprint } from '../../interfaces';
import { UpdateChartButton } from '../../common/buttons/chart/UpdateChartButton';
import { ButtonPermissionWrapper } from '../../common/buttons/ButtonPermissionWrapper';
import { ExcludeDaysButton } from '../../common/buttons/chart/ExcludeDaysButton';
import ExcludeDaysModal from './ExcludeDaysModal';
import { prepareSprintForm } from '../../utils/prepareSprintForm';
import axios from 'axios';
import { APIEndpoint } from '../../utils/endpoints/api';
import { AddChartLinesButton } from '../../common/buttons/chart/AddChartLinesButton';
import AddChartLinesModal from './AddChartLinesModal';
import { ChangeCapacityButton } from '../../common/buttons/ChangeCapacityButton';
import { ChangeCapacityModal } from './ChangeCapacityModal';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
);

interface SprintChartProps {
    options: ChartOptions;
    data: ChartData;
    sprint: ISprint,
    handleSettingTriggerChart: () => void
    handleSelectedSprint: (sprintId: number | string) => void
    handleButtonsDisable: () => void
    areButtonsDisabled: boolean
    hasPermission: boolean
    additionalChartLinesData: ChartLine[]
}

export function SprintChart({
    options,
    data,
    sprint,
    handleSelectedSprint,
    handleButtonsDisable,
    areButtonsDisabled,
    handleSettingTriggerChart,
    hasPermission,
    additionalChartLinesData
}: SprintChartProps) {
    const chartRef = useRef<HTMLDivElement>(null);

    return (
        <div className="col-xl-9 col-lg-7 col bg-light p-2 rounded overflow-scroll">
            <div ref={chartRef} className="chart-width">
                <Line options={options} data={data} />
            </div>
            <ButtonPermissionWrapper hasPermission={hasPermission}>
                <div className="d-flex flex-column flex-sm-row gap-1 mt-2 mb-1">
                    <DownloadPdfButton chartRef={chartRef} isDisabled={areButtonsDisabled} />
                    <UpdateChartButton
                        sprint={sprint}
                        isDisabled={areButtonsDisabled}
                        handleButtonsDisable={handleButtonsDisable}
                        handleSelectedSprint={handleSelectedSprint}
                        handleSettingTriggerChart={handleSettingTriggerChart}
                    />
                    <ExcludeDaysButton sprint={sprint} areButtonsDisabled={areButtonsDisabled} />
                    <ExcludeDaysModal
                        sprint={sprint}
                        handleSettingTriggerChart={handleSettingTriggerChart}
                        handleSelectedSprint={handleSelectedSprint}
                    />
                    <AddChartLinesButton sprint={sprint} areButtonsDisabled={areButtonsDisabled} />
                    <AddChartLinesModal
                        sprint={sprint}
                        handleSettingTriggerChart={handleSettingTriggerChart}
                        handleSelectedSprint={handleSelectedSprint}
                        additionalChartLinesData={additionalChartLinesData}
                    />
                    <ChangeCapacityButton areButtonsDisabled={areButtonsDisabled} />
                    <ChangeCapacityModal
                        data={data}
                        handleSettingTriggerChart={handleSettingTriggerChart}
                        handleSelectedSprint={handleSelectedSprint}
                        sprintId={sprint.id}
                        chartLinesSelect={additionalChartLinesData}
                    />
                </div>
            </ButtonPermissionWrapper>
        </div>
    );
}
