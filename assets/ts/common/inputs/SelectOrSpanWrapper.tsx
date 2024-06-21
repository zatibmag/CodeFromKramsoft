import * as React from 'react';
import { ReactNode } from 'react';

interface SelectOrSpanWrapperProps {
    hasPermission: boolean;
    value: string | number;
    label: string;
    children: ReactNode;
    id: string
    options?: {value: string | number, label: string}[];
}

export function SelectOrSpanWrapper({hasPermission, value, children, id, options, label}: SelectOrSpanWrapperProps) {
    return <div className="form-floating mt-1 mb-1">
        {hasPermission ? (children)
            : <span id={id} className="form-control">
                    {options.find(option => option.value === value)?.label}
            </span>}
        <label htmlFor={id}>{label}</label>
    </div>;
}
