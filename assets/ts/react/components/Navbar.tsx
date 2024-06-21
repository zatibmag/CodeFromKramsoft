import { LogoutButton } from '../../common/buttons/LogoutButton';
import * as React from 'react';
import { useContext } from 'react';
import { SettingsButton } from '../../common/buttons/SettingsButton';
import { ButtonPermissionWrapper } from '../../common/buttons/ButtonPermissionWrapper';
import { useAuth } from '../../hooks/useAuth';
import { useLoadingManager } from '../context/LoadingManager';
import { PageManagerContext, Pages } from '../context/PageManagerProvider';

export function Navbar() {
    const { isAdmin, isSuperAdmin } = useAuth();
    const { loading } = useLoadingManager();
    const { currentPage } = useContext(PageManagerContext);

    if (loading) {
        return <></>;
    }

    return <nav className="navbar navbar-expand-lg navbar-light bg-light w-100">
        <div className="container-fluid">
            <p className="navbar-brand">Burndown Chart</p>
            <button
                className="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span className="navbar-toggler-icon" />
            </button>
            <div className="collapse navbar-collapse" id="navbarNav">
                <ul className="navbar-nav ms-auto">
                    <li className="p-2">
                        <ButtonPermissionWrapper hasPermission={(currentPage !== Pages.Settings) && (isAdmin() || isSuperAdmin())}>
                            <SettingsButton />
                        </ButtonPermissionWrapper>
                    </li>
                    <li className="nav-item">
                        <LogoutButton />
                    </li>
                </ul>
            </div>
        </div>
    </nav>;
}
