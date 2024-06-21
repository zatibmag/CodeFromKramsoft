import * as React from 'react';
import { ReactChildren, ReactNode } from 'react';

interface ButtonPermissionWrapperProps {
    hasPermission: boolean;
    children: ReactNode;
}

export function ButtonPermissionWrapper({hasPermission, children}: ButtonPermissionWrapperProps) {
    if (!hasPermission) {
        return <></>;
    }

    return <>{children}</>;
}
