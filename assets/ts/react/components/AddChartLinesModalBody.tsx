import * as React from 'react';
import { useForm } from 'react-hook-form';
import { ChartLine, ISprint } from '../../interfaces';
import axios from 'axios';
import { APIEndpoint } from '../../utils/endpoints/api';
import { prepareAddChartLineForm } from '../../utils/prepareAddChartLineForm';
import { NewLineCapacityInput } from '../../common/inputs/chart/NewLineCapacityInput';
import { InputOrSpanWrapper } from '../../common/inputs/InputOrSpanWrapper';
import { Button, ButtonType } from '../../common/buttons/Button';

interface AddChartLinesModalBodyProps {
    sprint: ISprint;
    handleSettingTriggerChart: () => void;
    handleSelectedSprint: (sprintId: number | string) => void;
    formRef: React.MutableRefObject<HTMLFormElement>
    additionalChartLinesData: ChartLine[]
}

export function AddChartLinesModalBody({
    handleSelectedSprint,
    handleSettingTriggerChart,
    sprint,
    formRef,
    additionalChartLinesData
}: AddChartLinesModalBodyProps) {
    const {register, handleSubmit, formState: {errors}, setValue, getValues} = useForm();

    const onSubmit = async (sprintData: ISprint) => {
        const formData = prepareAddChartLineForm(getValues('newLineCapacity'));

        axios.post(`${APIEndpoint.addChartLine.replace('{sprintId}', sprint.id.toString())}`, formData)
            .then(function(response) {
                handleSelectedSprint(sprint.id);
                handleSettingTriggerChart();
            })
            .catch(function(errorNew) {
                console.error(errorNew);
            });
    };

    const handleRemoveChartLine = async (chartLineId: number) => {
        const apiUrlRemove = `${APIEndpoint.removeChartLine.replace('{id}', chartLineId.toString())}`;
        axios.post(apiUrlRemove).then(function(response) {
                handleSelectedSprint(sprint.id);
                handleSettingTriggerChart();
            })
            .catch(function(errorNew) {
                console.error(errorNew);
            });
    };

    return <>
        <form ref={formRef} onSubmit={handleSubmit(onSubmit)}>
            <InputOrSpanWrapper hasPermission={true} value={0} label={'Capacity for new perfect line'} id={'newLineCapacity'} >
                <NewLineCapacityInput defaultValue={0} register={register} errors={errors} />
            </InputOrSpanWrapper>

            {additionalChartLinesData.map((chartline: ChartLine) => {
                return <div key={chartline.id} className="d-flex flex-row justify-content-between border p-1">
                    <p className="my-2">Chartline with {chartline.chartPoints[0]?.y} capacity</p>
                    <Button
                        onClick={() => handleRemoveChartLine(chartline.id)}
                        className="btn btn-block btn-danger"
                        type={ButtonType.Button}
                        text={'Delete'}
                        data-bs-dismiss="modal"
                    />
                </div>
            })}
        </form>
    </>;
}
