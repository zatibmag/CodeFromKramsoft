import * as React from 'react';
import { useContext } from 'react';
import { PageManagerContext, Pages } from '../context/PageManagerProvider';
import { BackToHomeButton } from '../../common/buttons/BackToHomeButton';

interface SpinnerWrapperProps {
    isLoading: boolean;
}

export function SpinnerWrapper({isLoading, children}: React.PropsWithChildren<SpinnerWrapperProps>) {
    const { currentPage } = useContext(PageManagerContext);

    return (
        <>
            {isLoading
                ? (
                    <div className="d-flex justify-content-center align-items-center flex-column vh-100">
                        <div className="spinner-border" role="status">
                            <span className="sr-only"></span>
                        </div>
                        {currentPage !== Pages.Home &&
                            <BackToHomeButton
                                styles={'p-3'}
                                handleSelectedSprint={() => {
                                }}
                            />
                        }
                    </div>
                )
                : (
                    <>
                        {children}
                    </>
                )
            }
        </>
    );
}
