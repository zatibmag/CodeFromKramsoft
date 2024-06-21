import { Input } from '../Input';
import * as React from 'react';
import { FieldErrors, FieldValues, UseFormRegister } from 'react-hook-form';

interface StartAtInputProps {
    defaultValue: string | number;
    register: UseFormRegister<FieldValues>;
    errors: FieldErrors;
}

export function StartAtInput({register, defaultValue, errors}: StartAtInputProps) {
    return <Input
        type={'date'}
        defaultValue={defaultValue}
        name={"startAt"}
        register={register}
        id={"startAt"}
        placeholder={"Start at"}
        errors={errors}
        rules={{required: "Start at date is required"}}
    />;
}
