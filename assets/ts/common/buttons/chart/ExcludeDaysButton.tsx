import * as React from 'react';
import { ISprint } from '../../../interfaces';
import { Button, ButtonType } from '../Button';

interface ExcludeDaysButtonProps {
    sprint: ISprint;
    areButtonsDisabled: boolean;
}

export function ExcludeDaysButton({ areButtonsDisabled }: ExcludeDaysButtonProps) {

    return (
        <Button
            type={ButtonType.Button}
            className="btn btn-block btn-primary"
            text="Exclude days"
            isDisabled={areButtonsDisabled}
            data-bs-toggle="modal"
            data-bs-target="#excludeDaysModal"
        />
    );
}
