import * as React from 'react';

export enum ButtonType {
    Button = 'button',
    Submit = 'submit',
    Reset = 'reset',
}

interface ButtonProps {
    onClick?: (e: React.MouseEvent<HTMLButtonElement, MouseEvent>) => void;
    className: string;
    value?: string | number;
    isDisabled?: boolean;
    type: ButtonType;
    text: string;
}

export function Button({onClick, className, value, isDisabled, type, text, ...rest}: ButtonProps) {
    return <button
        type={type}
        className={className}
        value={value}
        onClick={onClick}
        disabled={isDisabled}
        {...rest}
    >{text}</button>;
}
