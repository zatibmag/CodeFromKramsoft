import * as React from 'react';
import axios from 'axios';
import Client from '../../../../lib/client';
import { Button, ButtonType } from '../Button';
import { IList, ISprint } from '../../../interfaces';
import { prepareSprintStoryForm } from '../../../utils/prepareSprintStoryForm';
import { APIEndpoint } from '../../../utils/endpoints/api';

interface UpdateChartDataButtonProps {
    sprint: ISprint,
    handleSettingTriggerChart: () => void
    handleSelectedSprint: (sprintId: number | string) => void
    handleButtonsDisable: () => void
    isDisabled: boolean
}

export function UpdateChartButton({
    sprint,
    handleSettingTriggerChart,
    handleSelectedSprint,
    handleButtonsDisable,
    isDisabled
}: UpdateChartDataButtonProps) {

    const handleUpdate = async (button: HTMLButtonElement) => {
        const client = new Client();
        await client.lists().then((lists) => {
            const formData = prepareSprintStoryForm(lists, sprint);
            const apiAddChartPointUrl = `${APIEndpoint.addChartPoint.replace('{sprintId}', sprint.id.toString())}`;

            axios.post(apiAddChartPointUrl, formData)
                .then(() => {
                    handleSettingTriggerChart();
                    handleSelectedSprint(sprint.id);
                })
                .catch(error => {
                    console.error('Error updating chart data:', error);
                });
        });
    };

    return <Button
        className={'btn btn-block btn-success'}
        type={ButtonType.Submit} text={'Update chart data'}
        onClick={(e) => {
            handleUpdate(e.target as HTMLButtonElement);
            handleButtonsDisable();
        }}
        isDisabled={isDisabled}
    />;
}
