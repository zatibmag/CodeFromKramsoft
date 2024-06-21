import { Input } from '../Input';
import * as React from 'react';
import { FieldErrors, FieldValues, UseFormRegister } from 'react-hook-form';

interface NewLineCapacityInputProps {
    defaultValue: string | number;
    register: UseFormRegister<FieldValues>;
    errors: FieldErrors;
}

export function NewLineCapacityInput({register, defaultValue, errors}: NewLineCapacityInputProps) {
    return <Input
        type={'number'}
        defaultValue={defaultValue}
        name={"newLineCapacity"}
        register={register}
        id={"newLineCapacity"}
        placeholder={"New Line Capacity"}
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
