import * as React from 'react';
import { Button, ButtonType } from './Button';

interface ChangeCapacityButtonProps {
    areButtonsDisabled: boolean;
}

export function ChangeCapacityButton({areButtonsDisabled}: ChangeCapacityButtonProps) {

    return (
        <Button
            type={ButtonType.Button}
            className="btn btn-block btn-info"
            text="Change capacity for a day"
            isDisabled={areButtonsDisabled}
            data-bs-toggle="modal"
            data-bs-target="#changeCapacityModal"
        />
    );
}
