import * as React from 'react';
import { UseFormRegister, FieldValues, RegisterOptions, FieldErrors } from 'react-hook-form';

interface InputProps {
    type: string;
    defaultValue: string | number;
    onChange?: (e: React.ChangeEvent<HTMLInputElement>) => void;
    name: string;
    register: UseFormRegister<FieldValues>;
    id: string;
    placeholder: string;
    rules?: RegisterOptions;
    errors: FieldErrors;
}

export function Input({
    type,
    defaultValue,
    onChange,
    name,
    register,
    id,
    placeholder,
    rules,
    errors
}: InputProps) {
    return (
        <>
            <input
                type={type}
                defaultValue={defaultValue}
                onChange={onChange}
                className={`form-control ${errors[name] ? 'is-invalid' : ''}`}
                name={name} {...register(name, rules)}
                id={id}
                placeholder={placeholder}
            />
            {errors[name] && <div className="invalid-feedback">{errors[name].message}</div>}
        </>

    );
}
