import * as ReactDOM from 'react-dom';
import * as React from 'react';
import 'bootstrap/dist/js/bootstrap.bundle.min';
import { App } from './react/App';

document.addEventListener('DOMContentLoaded', () => {
    const reactContainer = document.querySelector('.main');

    if (!reactContainer) {
        throw new Error('No container found');
    }

    ReactDOM.render(<App />, reactContainer);
});
