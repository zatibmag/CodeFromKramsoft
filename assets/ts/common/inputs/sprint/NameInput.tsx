import { Input } from '../Input';
import * as React from 'react';
import { FieldErrors, FieldValues, UseFormRegister } from 'react-hook-form';

interface NameInputProps {
    defaultValue: string | number;
    register: UseFormRegister<FieldValues>;
    errors: FieldErrors;
}

export function NameInput({register, defaultValue, errors}: NameInputProps) {
    return <Input
        type={'text'}
        defaultValue={defaultValue}
        name={"name"}
        register={register}
        id={"name"}
        placeholder={"Sprint name"}
        errors={errors}
        rules={{required: "Sprint name is required"}}
    />;
}
