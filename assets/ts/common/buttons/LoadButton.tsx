import * as React from 'react';
import { Button, ButtonType } from './Button';
import { ISprint } from '../../interfaces';

interface LoadButtonProps {
    sprints: ISprint[];
    handleLoadMore: () => void;
    areHomeButtonsDisabled: boolean;
    handleButtonsDisable: () => void;
    sprintsNumber: number;
}

export function LoadButton({
    sprints,
    handleLoadMore,
    handleButtonsDisable,
    areHomeButtonsDisabled,
    sprintsNumber
}: LoadButtonProps) {
    const handleLoadMoreClick = async () => {
        handleLoadMore();
        handleButtonsDisable();
    };

    return <>
        {sprints.length != sprintsNumber &&
            (
                <div className="card w-25 bg-transparent d-flex flex-column justify-content-center border-0">
                    <Button
                        className="btn btn-block btn-primary mt-1 mb-1"
                        onClick={() =>
                            handleLoadMoreClick()
                        }
                        type={ButtonType.Button}
                        isDisabled={areHomeButtonsDisabled}
                        text={areHomeButtonsDisabled
                            ? 'Loading...'
                            : 'Load More'
                        }
                    />
                </div>
            )
        }
    </>;
}
