import React, {Component} from 'react';
import {BrowserRouter as Router, Route} from 'react-router-dom'
import LogList from './log/LogList';
import LogDetail from './log/LogDetail';
import enUS from 'antd/lib/locale-provider/en_US';
import {Layout, LocaleProvider} from 'antd';
import './App.css';

const {Content} = Layout;

export class App extends Component {
    getChildContext() {
        const context = {
            channels: JSON.parse(this.props.channels),
            baseUrl: JSON.parse(this.props.serverUrl),
            serverUrl: JSON.parse(this.props.serverUrl) + 'api/'
        };

        return context;
    }

    render() {
        const serverUrl = JSON.parse(this.props.serverUrl);
        const parser = document.createElement('a');
        parser.href = serverUrl;

        return (
            <LocaleProvider locale={enUS}>
                <Router>
                    <Layout style={{minHeight: '100%'}}>
                        <Content style={{padding: '20px 50px'}}>
                            <Route breadcrumbName=":id" path={parser.pathname + ':id'} component={LogDetail}/>
                            <Route breadcrumbName="Home" exact path={parser.pathname} component={LogList}/>
                        </Content>
                    </Layout>
                </Router>
            </LocaleProvider>
        )
    }
}

App.childContextTypes = {
    channels: React.PropTypes.array,
    baseUrl: React.PropTypes.string,
    serverUrl: React.PropTypes.string
};

export default App;
