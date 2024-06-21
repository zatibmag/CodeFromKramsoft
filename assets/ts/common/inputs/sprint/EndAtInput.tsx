import { Input } from '../Input';
import * as React from 'react';
import { FieldErrors, FieldValues, UseFormRegister } from 'react-hook-form';

interface EndAtInputProps {
    defaultValue: string | number;
    register: UseFormRegister<FieldValues>;
    errors: FieldErrors;
    startAt: Date;
}

export function EndAtInput({register, defaultValue, errors, startAt}: EndAtInputProps) {
    return <Input
        type={'date'}
        defaultValue={defaultValue}
        name={"endAt"}
        register={register}
        id={"endAt"}
        placeholder={"End at"}
        errors={errors}
        rules={{
            required: "End at date is required",
            validate: value => value > startAt || 'End date must be after start date'
        }}
    />;
}
