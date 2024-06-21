import * as React from 'react';
import { ReactNode } from 'react';

interface ContentOrSpanWrapperProps {
    hasPermission: boolean;
    value: string | number;
    label: string;
    children: ReactNode;
    id: string
}

export function InputOrSpanWrapper({hasPermission, value, label, children, id}: ContentOrSpanWrapperProps) {
    return <div className="form-floating mt-1 mb-1">
        {hasPermission ? (children)
            : <span className="form-control" id={id}>
                {value}
            </span>}
        <label htmlFor={id}>{label}</label>
    </div>;
}
