import * as React from 'react';
import { ChartLine, ChartData } from '../../interfaces';
import { useForm } from 'react-hook-form';
import { APIEndpoint } from '../../utils/endpoints/api';
import axios from 'axios';
import { SelectOrSpanWrapper } from '../../common/inputs/SelectOrSpanWrapper';
import { CapacityDaySelect } from '../../common/inputs/sprint/CapacityDaySelect';
import { InputOrSpanWrapper } from '../../common/inputs/InputOrSpanWrapper';
import { CapacityInput } from '../../common/inputs/sprint/CapacityInput';
import { prepareCapacityUpdatedDayForm } from '../../utils/prepareCapacityDayForm';
import { ChartLineSelect } from '../../common/inputs/sprint/ChartLineSelect';
import _default from 'chart.js/dist/core/core.interaction';
import index = _default.modes.index;

interface ChangeCapacityModalBodyProps {
    sprintId: number | string;
    data: ChartData;
    handleSettingTriggerChart: () => void;
    handleSelectedSprint: (sprintId: number | string) => void;
    formRef: React.MutableRefObject<HTMLFormElement>;
    chartLinesSelect: ChartLine[];
}

export function ChangeCapacityModalBody({
    sprintId,
    data,
    handleSettingTriggerChart,
    handleSelectedSprint,
    formRef,
    chartLinesSelect
}: ChangeCapacityModalBodyProps) {
    const {register, handleSubmit, formState: {errors}, setValue, getValues} = useForm();

    const selectOptionsMap = (options: {id: string | number, title: string}[]) => {
        return options.map((option) => ({
            value: option.id,
            label: option.title,
        }));
    };

    const selectDaysOptionsMap = (options: {title: string}[]) => {
        return options.map((option) => ({
            value: option.title,
            label: option.title,
        }));
    };
    const onSubmit = async () => {
        const formData = prepareCapacityUpdatedDayForm(getValues('capacityDay'), getValues('capacity'));

        const capacityDaysAddUrl = APIEndpoint.capacityDaysAdd
            .replace('{sprintId}', sprintId.toString())
            .replace('{chartLineId}', getValues('chartLine'));

        try {
            await axios.post(capacityDaysAddUrl, formData)
                .then(() => {
                    handleSettingTriggerChart();
                    handleSelectedSprint(sprintId);
                });
        } catch (e) {
            console.error(e);
        }
    };

    const transformedLabels = data.labels.slice(0, -1).map((label) => ({title: label.toString()}));
    const transformedChartLineTitles = chartLinesSelect.map(line => {
        const capacity = line.chartPoints[0]?.y || 'unknown';

        return {
            id: line.id,
            title: `Perfect for capacity = ${capacity}`
        };
    });
    return (
        <>
            <form ref={formRef} onSubmit={handleSubmit(onSubmit)}>
                <SelectOrSpanWrapper
                    hasPermission={true}
                    value={transformedChartLineTitles.length > 0 ? transformedChartLineTitles[0].title : "No Chart Lines"}
                    label="Day to change capacity"
                    id="chartLine"
                    options={selectOptionsMap(transformedChartLineTitles)}
                >
                    <ChartLineSelect
                        register={register}
                        value={transformedChartLineTitles.length > 0 ? transformedChartLineTitles[0].title : "No Chart Lines"}
                        options={transformedChartLineTitles.length > 0 ? selectOptionsMap(transformedChartLineTitles) : [{ value: '', label: 'No Chart Lines' }]}
                    />
                </SelectOrSpanWrapper>
                <SelectOrSpanWrapper
                    hasPermission={true}
                    value={data.labels[0]}
                    label="Day to change capacity"
                    id="capacityDay"
                    options={selectDaysOptionsMap(transformedLabels)}
                >
                    <CapacityDaySelect
                        register={register}
                        value={data.labels[0]}
                        options={selectDaysOptionsMap(transformedLabels)}
                    />
                </SelectOrSpanWrapper>
                <InputOrSpanWrapper
                    hasPermission={true}
                    value={0}
                    label={'Capacity'}
                    id={'capacity'}
                >
                    <CapacityInput
                        defaultValue={0}
                        register={register}
                        errors={errors}
                    />
                </InputOrSpanWrapper>
            </form>
        </>
    );
}
