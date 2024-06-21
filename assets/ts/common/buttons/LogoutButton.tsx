import * as React from 'react';
import { Button, ButtonType } from './Button';
import { AppEndpoint } from '../../utils/endpoints/app';

export function LogoutButton() {
    return <div className="m-2">
        <a href={AppEndpoint.logout}>
            <Button
            className={'btn btn-block btn-danger'}
            type={ButtonType.Button}
            text={'Logout'} />
        </a>
    </div>;
}
