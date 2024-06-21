import * as React from 'react';
import { useRef } from 'react';
import { ChartLine, ChartData } from '../../interfaces';
import { Button, ButtonType } from '../../common/buttons/Button';
import { ChangeCapacityModalBody } from './ChangeCapacityModalBody';

interface ChangeCapacityModalProps {
    data: ChartData;
    handleSettingTriggerChart: () => void
    handleSelectedSprint: (sprintId: number | string) => void;
    sprintId: number | string
    chartLinesSelect: ChartLine[]
}

export function ChangeCapacityModal({
    data,
    handleSelectedSprint,
    handleSettingTriggerChart,
    sprintId,
    chartLinesSelect
}: ChangeCapacityModalProps) {
    const formRef = useRef<HTMLFormElement>(null);

    return (
        <>
            <div
                className="modal fade"
                id="changeCapacityModal"
                data-bs-backdrop="static"
                data-bs-keyboard="false"
                tabIndex={-1}
                aria-labelledby="changeCapacityModalLabel"
                aria-hidden="true"
            >
                <div className="modal-dialog modal-dialog-scrollable">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title" id="changeCapacityModalLabel">Change capacity</h5>
                            <Button
                                className="btn-close"
                                type={ButtonType.Button}
                                text={""}
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            />
                        </div>
                        <div className="modal-body">
                            <ChangeCapacityModalBody
                                sprintId={sprintId}
                                data={data}
                                handleSettingTriggerChart={handleSettingTriggerChart}
                                handleSelectedSprint={handleSelectedSprint}
                                formRef={formRef}
                                chartLinesSelect={chartLinesSelect}
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
}
