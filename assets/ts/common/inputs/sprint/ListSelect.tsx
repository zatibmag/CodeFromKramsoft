import * as React from 'react';
import { Select } from '../Select';
import { FieldValues, UseFormRegister } from 'react-hook-form';
import { IList } from '../../../interfaces';

interface ListSelectProps {
    onChange?: (e: React.ChangeEvent<HTMLSelectElement>) => void;
    register: UseFormRegister<FieldValues>;
    value: string;
    options?: {value: string | number, label: string}[];
}

export function ListSelect({
    value,
    register,
    options,
    onChange
}: ListSelectProps) {
    return <Select
        value={value}
        name="listDoneId"
        register={register}
        id="listDoneId"
        placeholder="Select list"
        options={options}
        onChange={onChange}
    />
}
