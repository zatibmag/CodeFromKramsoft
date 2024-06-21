import { ISprint } from '../interfaces';

const appendDateToSprintForm = (formData: FormData, index: number, date: Date): void => {
    formData.append(`sprint_days[excludedDays][${index}][date][month]`, (date.getMonth() + 1).toString());
    formData.append(`sprint_days[excludedDays][${index}][date][day]`, date.getDate().toString());
    formData.append(`sprint_days[excludedDays][${index}][date][year]`, date.getFullYear().toString());
};

export const prepareExcludedDaysForm = (excludedDays: Date[], sprint: ISprint): FormData => {
    const formData = new FormData();

    excludedDays.forEach((excludedDay, index) => {
        const date = new Date(excludedDay);
        appendDateToSprintForm(formData, index, date);
    });

    return formData;
};
