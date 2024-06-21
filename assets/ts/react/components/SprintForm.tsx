import * as React from 'react';
import { useForm } from 'react-hook-form';
import axios from 'axios';
import { IList, ISprint } from '../../interfaces';
import { useEffect } from 'react';
import { UpdateSprintButton } from '../../common/buttons/sprint/UpdateSprintButton';
import { DeleteSprintButton } from '../../common/buttons/sprint/DeleteSprintButton';
import { CreateSprintButton } from '../../common/buttons/sprint/CreateSprintButton';
import { NameInput } from '../../common/inputs/sprint/NameInput';
import { CapacityInput } from '../../common/inputs/sprint/CapacityInput';
import { StartAtInput } from '../../common/inputs/sprint/StartAtInput';
import { EndAtInput } from '../../common/inputs/sprint/EndAtInput';
import { ListSelect } from '../../common/inputs/sprint/ListSelect';
import { CapacityTypeSelect } from '../../common/inputs/sprint/CapacityTypeSelect';
import { InputOrSpanWrapper } from '../../common/inputs/InputOrSpanWrapper';
import { SelectOrSpanWrapper } from '../../common/inputs/SelectOrSpanWrapper';
import { ButtonPermissionWrapper } from '../../common/buttons/ButtonPermissionWrapper';
import { APIEndpoint } from '../../utils/endpoints/api';
import { prepareSprintForm } from '../../utils/prepareSprintForm';

interface SprintFormProps {
    sprints: ISprint[];
    sprint: ISprint;
    areButtonsDisabled: boolean;
    hasPermission: boolean;
    lists: IList[];
    selectedList: string | null;
    selectedSprintId: string | number;
    setSelectedList: (listId: string | null) => void;
    handleSelectedSprint: (sprintId: string | number) => void;
    handleSettingLimit: (limit: number) => void;
    handleSettingTriggerChart: () => void;
    handleButtonsDisable: () => void;
}

export function SprintForm({
    sprints,
    sprint,
    lists,
    hasPermission,
    selectedList,
    setSelectedList,
    selectedSprintId,
    handleSelectedSprint,
    handleSettingLimit,
    handleSettingTriggerChart,
    areButtonsDisabled,
    handleButtonsDisable
}: SprintFormProps) {

    const { register, handleSubmit, formState: { errors }, setValue, getValues } = useForm();

    useEffect(() => {
        if (lists.length > 0 && selectedList === null) {
            setSelectedList(sprint?.listDoneId);
        }
    }, [lists, selectedList, setSelectedList]);


    const onSubmit = async (sprintData: ISprint) => {
        if (!hasPermission) {
            return;
        }

        if (selectedSprintId !== "newSprint") {
            sprintData.capacityType = sprint.capacityType
        }

        handleButtonsDisable();
        const formData = prepareSprintForm(sprintData);

        if (selectedSprintId === 'newSprint') {
            axios.post(`${APIEndpoint.sprintNew}`, formData)
                .then(function(response) {
                    const sprint: ISprint = response.data;
                    handleSelectedSprint(sprint.id);
                    handleSettingLimit(5);
                    handleSettingTriggerChart();
                })
                .catch(function(errorNew) {
                    console.error(errorNew);
                });
        } else {
            const apiUpdateUrl = `${APIEndpoint.sprintUpdate.replace('{sprintId}', selectedSprintId.toString())}`;
            axios.post(apiUpdateUrl, formData)
                .then(function(response) {
                    const sprint: ISprint = response.data;
                    handleSettingLimit(5);
                    handleSelectedSprint(sprint.id);
                    handleSettingTriggerChart();
                })
                .catch(error => {
                    console.error(error);
                });
        }
    };

    const selectOptionsMap = (options: {id: string | number, title: string}[]) => {
        return options.map((option) => ({
            value: option.id,
            label: option.title,
        }));
    }

    const valueTypeSelectProps = {
        options: selectOptionsMap([
            {id: 0, title: 'Story Points'},
            {id: 1, title: 'Tasks'}
        ]),
        onChange: (e: React.ChangeEvent<HTMLSelectElement>) => {
            setValue("valueType", e.target.value);
        }
    }

    return (
        <div className="ChartForm">
            <form onSubmit={handleSubmit(onSubmit)} noValidate>
                <InputOrSpanWrapper hasPermission={hasPermission} value={sprint?.name} label="Sprint name" id="name">
                    <NameInput register={register} defaultValue={sprint?.name} errors={errors} />
                </InputOrSpanWrapper>

                <InputOrSpanWrapper hasPermission={hasPermission} value={sprint?.capacity} label="Capacity" id="capacity">
                    <CapacityInput register={register} defaultValue={sprint?.capacity} errors={errors} />
                </InputOrSpanWrapper>

                <InputOrSpanWrapper hasPermission={hasPermission} value={sprint?.startAt} label="Start at" id="startAt">
                    <StartAtInput register={register} defaultValue={sprint?.startAt} errors={errors} />
                </InputOrSpanWrapper>

                <InputOrSpanWrapper hasPermission={hasPermission} value={sprint?.endAt} label="End at" id="endAt">
                    <EndAtInput register={register} defaultValue={sprint?.endAt} errors={errors} startAt={getValues("startAt")}/>
                </InputOrSpanWrapper>

                <SelectOrSpanWrapper hasPermission={hasPermission} value={sprint?.listDoneId} label="Lists" id="listDoneId" options={selectOptionsMap(lists)}>
                    <ListSelect register={register} value={sprint?.listDoneId} options={selectOptionsMap(lists)} />
                </SelectOrSpanWrapper>

                <SelectOrSpanWrapper hasPermission={selectedSprintId === "newSprint"} value={sprint?.capacityType} label="Capacity type" id="capacityType" options={valueTypeSelectProps.options}>
                    <CapacityTypeSelect register={register} {...valueTypeSelectProps} value={sprint?.capacityType} />
                </SelectOrSpanWrapper>
                {(selectedSprintId !== 'newSprint'
                        ? <ButtonPermissionWrapper hasPermission={hasPermission}>
                            <UpdateSprintButton isDisabled={areButtonsDisabled}/>
                            <DeleteSprintButton
                                sprints={sprints}
                                selectedSprintId={selectedSprintId}
                                handleSelectedSprint={handleSelectedSprint}
                                handleSettingLimit={handleSettingLimit}
                                isDisabled={areButtonsDisabled}
                                handleButtonsDisable={handleButtonsDisable}
                            />
                        </ButtonPermissionWrapper>
                        : <ButtonPermissionWrapper hasPermission={hasPermission}>
                            <CreateSprintButton isDisabled={areButtonsDisabled}/>
                        </ButtonPermissionWrapper>
                )
                }
            </form>
        </div>
    );
}
