import React from "react";
import ReactDOM from "react-dom";
import App from './App';

const serverUrl = JSON.stringify('http://localhost:8000/monolog/');
const channels = JSON.stringify(['security', 'request']);

ReactDOM.render(
    <App serverUrl={serverUrl} channels={channels}/>,
    document.getElementById('root')
);
