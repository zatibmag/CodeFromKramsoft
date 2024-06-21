import { ISprint } from '../interfaces';

export const prepareSprintForm = (sprintData: ISprint): FormData => {
    const startAtDate = new Date(sprintData.startAt);
    const endAtDate = new Date(sprintData.endAt);
    const formData = new FormData();

    formData.append('sprint[name]', sprintData.name);
    formData.append('sprint[capacity]', sprintData.capacity.toString());
    formData.append('sprint[listDoneId]', sprintData.listDoneId);
    formData.append('sprint[capacityType]', sprintData.capacityType.toString());
    appendDateToSprintForm(formData, 'startAt', startAtDate);
    appendDateToSprintForm(formData, 'endAt', endAtDate);

    return formData;
};

const appendDateToSprintForm = (formData: FormData, fieldName: string, date: Date): void => {
    formData.append(`sprint[${fieldName}][month]`, (date.getMonth() + 1).toString());
    formData.append(`sprint[${fieldName}][day]`, date.getDate().toString());
    formData.append(`sprint[${fieldName}][year]`, date.getFullYear().toString());
};
