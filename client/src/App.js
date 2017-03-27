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
            serverUrl: JSON.parse(this.props.serverUrl)
        };

        return context;
    }

    render() {
        return (
            <LocaleProvider locale={enUS}>
                <Router>
                    <Layout style={{minHeight: '100%'}}>
                        <Content style={{padding: '0 50px'}}>
                            <Layout style={{background: '#fff'}}>
                                <Route breadcrumbName=":id" path="/:id" component={LogDetail}/>
                                <Route breadcrumbName="Home" exact path="/" component={LogList}/>
                            </Layout>
                        </Content>
                    </Layout>
                </Router>
            </LocaleProvider>
        )
    }
}

App.childContextTypes = {
    channels: React.PropTypes.array,
    serverUrl: React.PropTypes.string
};

export default App;
