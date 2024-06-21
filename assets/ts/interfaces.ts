export interface ChartData {
    labels: (string | number)[];
    datasets: Dataset[];
}

export interface ChartPoint {
    x: string|number;
    y: string|number;
}

export interface ChartLine {
    id: number,
    chartPoints: ChartPoint[]
}

export interface Dataset {
    label: string;
    data: ChartPoint[];
    borderColor: string;
    backgroundColor: string;
}

export interface ChartOptions {
    responsive: boolean;
    plugins: {
        legend: {
            position: 'top' | 'bottom' | 'left' | 'right';
        };
        title: {
            display: boolean;
            text: string;
        };
    };
    scales: {
        y: {
            beginAtZero: boolean
            min: number
        }
    };
}

export interface ISprint {
    id: number;
    name: string;
    capacity: number;
    startAt: string;
    endAt: string;
    listDoneId: string;
    capacityType: number;
    excludedDays: {date: Date}[];
}

export interface IList {
    id: string;
    tasks: ITask[];
    title: string;
}

export interface IActivity {
    id: string;
    list: string;
    movedAt: string;
}

export interface ITask {
    activity: IActivity[];
    currentList: string;
    id: string;
    title: string;
    storyPoints: number;
}


