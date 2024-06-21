import * as React from 'react';
import { FieldValues, UseFormRegister } from 'react-hook-form';

interface CheckboxListItemProps {
    day: Date,
    register: UseFormRegister<FieldValues>,
    handleCheckboxChange: (day: Date) => void
}

export function CheckboxListItem({
    day,
    register,
    handleCheckboxChange
}: CheckboxListItemProps) {
    const dayString = day.toISOString().split('T')[0];

    return <>
        <div className="col" key={dayString}>
            <li className="list-inline-item">
                <div className="form-check">
                    <label className="form-check-label"> <input
                        className="form-check-input"
                        type="checkbox"{...register(day.toDateString())}
                        value={dayString}
                        onChange={() => handleCheckboxChange(day)}
                    /> {dayString}
                    </label>
                </div>
            </li>
        </div>
    </>;
}
