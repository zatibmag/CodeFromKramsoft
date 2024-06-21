import * as React from 'react';
import { Select } from '../Select';
import { FieldValues, UseFormRegister } from 'react-hook-form';

interface CapacityDaySelectProps {
    onChange?: (e: React.ChangeEvent<HTMLSelectElement>) => void;
    register: UseFormRegister<FieldValues>;
    value: number | string;
    options?: {value: string | number, label: string}[];
}

export function CapacityDaySelect({
    value,
    register,
    options,
    onChange
}: CapacityDaySelectProps) {
    return <Select
        value={value}
        name="capacityDay"
        register={register}
        id="capacityDay"
        placeholder="Day to change capacity"
        options={options}
        onChange={onChange}
    />;
}
