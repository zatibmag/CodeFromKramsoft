import * as React from 'react';
import { useRef } from 'react';
import { ChartLine, ISprint } from '../../interfaces';
import { Button, ButtonType } from '../../common/buttons/Button';
import { AddChartLinesModalBody } from './AddChartLinesModalBody';

interface AddChartLinesModalProps {
    sprint: ISprint;
    handleSettingTriggerChart: () => void;
    handleSelectedSprint: (sprintId: number | string) => void;
    additionalChartLinesData: ChartLine[]
}

const AddChartLinesModal = ({
    sprint,
    handleSettingTriggerChart,
    handleSelectedSprint,
    additionalChartLinesData
}: AddChartLinesModalProps) => {
    const formRef = useRef<HTMLFormElement>(null);

    return (
        <>
            <div
                className="modal fade"
                id="addChartLinesModal"
                data-bs-backdrop="static"
                data-bs-keyboard="false"
                tabIndex={-1}
                aria-labelledby="addChartLinesModalLabel"
                aria-hidden="true"
            >
                <div className="modal-dialog modal-dialog-scrollable">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title" id="addChartLinesModalLabel">Add new chart line</h5>
                            <Button
                                className="btn-close"
                                type={ButtonType.Button}
                                text={""}
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            />
                        </div>
                        <div className="modal-body">
                            <AddChartLinesModalBody
                                sprint={sprint}
                                handleSelectedSprint={handleSelectedSprint}
                                handleSettingTriggerChart={handleSettingTriggerChart}
                                formRef={formRef}
                                additionalChartLinesData={additionalChartLinesData}
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
                                type={ButtonType.Submit}
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

export default AddChartLinesModal;
