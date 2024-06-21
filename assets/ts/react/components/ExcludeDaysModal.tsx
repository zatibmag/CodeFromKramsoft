import * as React from 'react';
import { useRef } from 'react';
import { ExcludeDaysModalBody } from './ExcludeDaysModalBody';
import { ISprint } from '../../interfaces';
import { Button, ButtonType } from '../../common/buttons/Button';

interface ExcludeDaysModal {
    sprint: ISprint;
    handleSettingTriggerChart: () => void;
    handleSelectedSprint: (sprintId: number | string) => void;
}

const ExcludeDaysModal = ({
    sprint,
    handleSettingTriggerChart,
    handleSelectedSprint,
}: ExcludeDaysModal) => {
    const formRef = useRef<HTMLFormElement>(null);

    return (
        <>
            <div
                className="modal fade"
                id="excludeDaysModal"
                data-bs-backdrop="static"
                data-bs-keyboard="false"
                tabIndex={-1}
                aria-labelledby="excludeDaysModalLabel"
                aria-hidden="true"
            >
                <div className="modal-dialog modal-dialog-scrollable">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title" id="excludeDaysModalLabel">Exclude days</h5>
                            <Button
                                className="btn-close"
                                type={ButtonType.Button}
                                text={""}
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            />
                        </div>
                        <div className="modal-body">
                            <ExcludeDaysModalBody
                                sprint={sprint}
                                selectedDays={sprint.excludedDays}
                                handleSettingTriggerChart={handleSettingTriggerChart}
                                handleSelectedSprint={handleSelectedSprint}
                                formRef={formRef}
                            />
                        </div>
                        <div className="modal-footer">
                            <Button
                                className="btn btn-secondary"
                                type={ButtonType.Button}
                                text={"Close"}
                                data-bs-dismiss="modal"
                            />
                            <Button
                                onClick={() => formRef.current.requestSubmit()}
                                className="btn btn-primary"
                                type={ButtonType.Button}
                                text={"Submit"}
                                data-bs-dismiss="modal"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default ExcludeDaysModal;
