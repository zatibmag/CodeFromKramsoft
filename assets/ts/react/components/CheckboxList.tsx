import * as React from 'react';
import { FieldValues, UseFormRegister } from 'react-hook-form';
import { CheckboxListItem } from './CheckboxListItem';

interface CheckboxListProps {
    days: Date[],
    register: UseFormRegister<FieldValues>,
    handleCheckboxChange: (day: Date) => void
}

export function CheckboxList({
    days,
    register,
    handleCheckboxChange
}: CheckboxListProps) {

    return <>
        <div className="row row-cols-2 overflow-scroll">
            {days.map(day => {
                return <CheckboxListItem key={day.toISOString()} day={day} register={register} handleCheckboxChange={handleCheckboxChange} />
            })}
        </div>
    </>;
}
