import * as React from 'react';

export function NoSprintsMessage() {
    return <div className="border border-secondary d-flex flex-column justify-content-center align-items-center p-5">
        <div className="p-2">
            <p>There are no sprints,</p>
        </div>
        <div className="p-2">
            <p>wait until someone creates them.</p>
        </div>
    </div>;
}
