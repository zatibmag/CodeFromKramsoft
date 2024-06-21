import * as React from 'react';
import { useState, useEffect } from 'react';
import { ISprint } from '../../interfaces';
import { FieldValues, useForm } from 'react-hook-form';
import { prepareExcludedDaysForm } from '../../utils/prepareExcludedDaysForm';
import { APIEndpoint } from '../../utils/endpoints/api';
import axios from 'axios';
import { CheckboxList } from './CheckboxList';

interface ExcludeDaysModalBodyProps {
    sprint: ISprint;
    selectedDays: { date: Date }[];
    handleSettingTriggerChart: () => void;
    handleSelectedSprint: (sprintId: number | string) => void;
    formRef: React.MutableRefObject<HTMLFormElement>
}

const getDaysInSprint = (sprint: ISprint) => {
    const days = [];
    const currentDate = new Date(sprint.startAt);
    const endDate = new Date(sprint.endAt);

    for (; currentDate <= endDate; currentDate.setDate(currentDate.getDate() + 1)) {
        days.push(new Date(currentDate));
    }

    return days;
};

const hasWeekendSelected = (selectedDays: { date: Date }[]) => {
    return selectedDays.some(day => {
        const date = new Date(day.date);
        return date.getDay() === 0 || date.getDay() === 6;
    });
};

export function ExcludeDaysModalBody({ sprint, selectedDays, handleSettingTriggerChart, handleSelectedSprint, formRef }: ExcludeDaysModalBodyProps) {
    const { register, handleSubmit, setValue, getValues } = useForm<FieldValues>();
    const days = getDaysInSprint(sprint);
    const [weekendsExcluded, setWeekendsExcluded] = useState(() => hasWeekendSelected(selectedDays));

    const apiExcludeDaysUrl = APIEndpoint.excludeDays.replace('{sprintId}', sprint.id.toString());
    const apiExcludedDaysRemoveUrl = APIEndpoint.excludedDaysRemove.replace('{sprintId}', sprint.id.toString());

    useEffect(() => {
        const convertedDays = selectedDays.map(day => new Date(day.date));
        convertedDays.forEach(day => {
            setValue(day.toDateString(), true);
        });
    }, [selectedDays, setValue]);

    const onSubmit = (selectedDays: Date[]) => {
        const requestPromise = (selectedDays && selectedDays.length > 0)
            ? axios.post(apiExcludeDaysUrl, prepareExcludedDaysForm(selectedDays, sprint))
            : axios.post(apiExcludedDaysRemoveUrl);

        requestPromise
            .then(() => {
                handleSettingTriggerChart();
                if (!selectedDays || selectedDays.length === 0) {
                    handleSelectedSprint(sprint.id.toString());
                }
            })
            .catch(error => {
                console.error('Error updating chart data:', error);
            });
    };

    const handleCheckboxListSubmit = (formData: FieldValues) => {
        const selectedDays = Object.keys(formData)
            .filter(key => formData[key])
            .map(key => new Date(key));
        onSubmit(selectedDays);
    };

    const handleWeekendCheckboxToggle = (checkbox: HTMLInputElement, date: Date) => {
        if (weekendsExcluded && !checkbox.checked) return;
        if (!weekendsExcluded && checkbox.checked) return;
        checkbox.checked = !checkbox.checked;
        handleCheckboxChange(date);
    };

    const handleCheckboxChange = (day: Date) => {
        const dayString = day.toDateString();
        const isChecked = getValues(dayString);

        setValue(dayString, !isChecked);
    };

    const excludeWeekends = () => {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach((checkbox: HTMLInputElement) => {
            const date = new Date(checkbox.value);
            if (date.getDay() === 0 || date.getDay() === 6) {
                handleWeekendCheckboxToggle(checkbox, date);
            }
        });
    };

    const handleToggleWeekends = () => {
        setWeekendsExcluded(!weekendsExcluded);
        excludeWeekends();
    };

    return (
        <>
            <form ref={formRef} onSubmit={handleSubmit(handleCheckboxListSubmit)}>
                <div>
                    <CheckboxList days={days} register={register} handleCheckboxChange={handleCheckboxChange} />
                </div>
            </form>
            <button
                className="btn btn-primary mt-2"
                onClick={handleToggleWeekends}
            >
                {weekendsExcluded ? "Unselect Weekends" : "Select Weekends"}
            </button>
        </>
    );
}
