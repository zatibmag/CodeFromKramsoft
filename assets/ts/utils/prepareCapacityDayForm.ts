const appendDateToSprintForm = (formData: FormData, date: Date): void => {
    formData.append(`capacity_day_chart_point[date][month]`, (date.getMonth() + 1).toString());
    formData.append(`capacity_day_chart_point[date][day]`, date.getDate().toString());
    formData.append(`capacity_day_chart_point[date][year]`, date.getFullYear().toString());
};

export const prepareCapacityUpdatedDayForm = (day: Date, value: number): FormData => {
    const formData = new FormData();

    const date = new Date(day);
    appendDateToSprintForm(formData, date);
    formData.append(`capacity_day_chart_point[value]`, value.toString());

    return formData;
};
