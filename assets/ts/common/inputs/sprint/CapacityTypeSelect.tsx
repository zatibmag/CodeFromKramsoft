import * as React from 'react';
import { Select } from '../Select';
import { FieldValues, UseFormRegister } from 'react-hook-form';

interface CapacityTypeSelectProps {
    onChange?: (e: React.ChangeEvent<HTMLSelectElement>) => void;
    register: UseFormRegister<FieldValues>;
    value: number | string;
    options?: {value: string | number, label: string}[];
}

export function CapacityTypeSelect({
    value,
    register,
    options,
    onChange
}: CapacityTypeSelectProps) {
    return <Select
        value={value}
        name="capacityType"
        register={register}
        id="capacityType"
        placeholder="Select Type"
        options={options}
        onChange={onChange}
    />
}
