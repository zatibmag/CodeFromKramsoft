import * as React from 'react';
import { Select } from '../Select';
import { FieldValues, UseFormRegister } from 'react-hook-form';

interface ChartLineSelectProps {
    onChange?: (e: React.ChangeEvent<HTMLSelectElement>) => void;
    register: UseFormRegister<FieldValues>;
    value: number | string;
    options?: {value: string | number, label: string}[];
}

export function ChartLineSelect({
    value,
    register,
    options,
    onChange
}: ChartLineSelectProps) {
    return <Select
        value={value}
        name="chartLine"
        register={register}
        id="chartLine"
        placeholder="Chart line to change capacity for a day"
        options={options}
        onChange={onChange}
    />;
}
