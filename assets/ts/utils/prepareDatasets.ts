import { ChartLine, Dataset } from '../interfaces';

enum DatasetColors {
    '{"backgroundColor": "rgba(255, 206, 86, 0.5)", "borderColor": "rgb(255, 206, 86)"}',
    '{"backgroundColor": "rgba(153, 102, 255, 0.5)", "borderColor": "rgb(153, 102, 255)"}',
    '{"backgroundColor": "rgba(255, 159, 64, 0.5)", "borderColor": "rgb(255, 159, 64)"}',
    '{"backgroundColor": "rgba(199, 199, 199, 0.5)", "borderColor": "rgb(199, 199, 199)"}',
    '{"backgroundColor": "rgba(83, 102, 255, 0.5)", "borderColor": "rgb(83, 102, 255)"}',
    '{"backgroundColor": "rgba(255, 102, 204, 0.5)", "borderColor": "rgb(255, 102, 204)"}',
    '{"backgroundColor": "rgba(102, 255, 102, 0.5)", "borderColor": "rgb(102, 255, 102)"}',
    '{"backgroundColor": "rgba(255, 153, 102, 0.5)", "borderColor": "rgb(255, 153, 102)"}',
    '{"backgroundColor": "rgba(75, 0, 130, 0.5)", "borderColor": "rgb(75, 0, 130)"}',
    '{"backgroundColor": "rgba(255, 0, 255, 0.5)", "borderColor": "rgb(255, 0, 255)"}',
    '{"backgroundColor": "rgba(0, 128, 128, 0.5)", "borderColor": "rgb(0, 128, 128)"}',
    '{"backgroundColor": "rgba(255, 215, 0, 0.5)", "borderColor": "rgb(255, 215, 0)"}'
}

export function prepareDatasets(currentChartLine: ChartLine, perfectChartLine: ChartLine, lines: ChartLine[]) {
    const datasets: Dataset[] = [];

    datasets.push({
        label:           'Perfect',
        data:            perfectChartLine.chartPoints.map(point => ({ x: point.x, y: point.y })),
        borderColor:     'rgb(53, 162, 235)',
        backgroundColor: 'rgba(53, 162, 235, 0.5)',
    });

    datasets.push({
        label:           'Current',
        data:            currentChartLine.chartPoints.map(point => ({ x: point.x, y: point.y })),
        borderColor:     'rgb(255, 99, 132)',
        backgroundColor: 'rgba(255, 99, 132, 0.5)',
    });

    let lineNumber = 0;
    lines.forEach((line: ChartLine) => {
        const {borderColor, backgroundColor} = JSON.parse(DatasetColors[lineNumber]);

        datasets.push({
            label:           `Perfect for capacity = ${line.chartPoints[0]?.y || 'unknown'}`,
            data:            line.chartPoints.map(point => ({ x: point.x, y: point.y })),
            borderColor:     borderColor,
            backgroundColor: backgroundColor,
        });

        lineNumber++;
    });

    return datasets;
}
