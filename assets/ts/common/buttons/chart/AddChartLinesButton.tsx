import * as React from 'react';
import { ISprint } from '../../../interfaces';
import { Button, ButtonType } from '../Button';

interface AddChartLinesButtonProps {
    sprint: ISprint;
    areButtonsDisabled: boolean;
}

export function AddChartLinesButton({ areButtonsDisabled }: AddChartLinesButtonProps) {

    return (
        <Button
            type={ButtonType.Button}
            className="btn btn-block btn-primary"
            text="Add chart lines"
            isDisabled={areButtonsDisabled}
            data-bs-toggle="modal"
            data-bs-target="#addChartLinesModal"
        />
    );
}
