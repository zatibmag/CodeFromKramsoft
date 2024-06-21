export const prepareAddChartLineForm = (capacity: number): FormData => {
    const formData = new FormData();

    formData.append('sprint_chart_line[capacity]', capacity.toString());

    return formData;
};

