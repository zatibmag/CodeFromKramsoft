import { Input } from '../Input';
import * as React from 'react';
import { FieldErrors, FieldValues, UseFormRegister } from 'react-hook-form';

interface CapacityInputProps {
    defaultValue: string | number;
    register: UseFormRegister<FieldValues>;
    errors: FieldErrors;
}

export function CapacityInput({register, defaultValue, errors}: CapacityInputProps) {
    return <Input
        type={'number'}
        defaultValue={defaultValue}
        name={"capacity"}
        register={register}
        id={"capacity"}
        placeholder={"Capacity"}
        errors={errors}
        rules={{
            required: 'Capacity is required',
            min: {
                value: 0,
                message: 'Capacity must be greater than or equal to 0'
            }
        }}
    />;
}
