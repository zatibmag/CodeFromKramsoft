import * as React from 'react';
import { FieldValues, UseFormRegister } from 'react-hook-form';

interface SelectProps {
    value: string | number;
    onChange?: (e: React.ChangeEvent<HTMLSelectElement>) => void;
    name: string;
    register: UseFormRegister<FieldValues>;
    id: string;
    placeholder: string;
    options?: {value: string | number, label: string}[];
}

export function Select({
    onChange,
    name,
    register,
    id,
    value,
    options=[]
}: SelectProps) {
    return <select
        {...register(name)}
        onChange={onChange}
        defaultValue={value}
        id={id}
        className="form-select"
        name={name}
    >
        {options.map((option) => (
            <option key={option.value} value={option.value}>
                {option.label}
            </option>
        ))}
    </select>;
}
