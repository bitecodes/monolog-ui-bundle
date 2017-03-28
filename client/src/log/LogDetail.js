import React, {Component} from 'react';
import {Observable} from "rxjs/bundles/Rx";
import {Button, Layout, Spin} from 'antd';
import {Link} from 'react-router-dom';
import LogLevelTag from './LogLevelTag';
import TreeObject from './TreeObject';
import moment from 'moment';

export default class LogDetail extends Component {

    constructor(props) {
        super(props);

        this.state = {
            log: null,
            loading: true
        }
    }

    componentDidMount() {
        const url = this.context.serverUrl + this.props.match.params.id;

        Observable
            .ajax(url)
            .map((ajax) => ajax.response.data)
            .subscribe(log => this.setState({log, loading: false}));
    }

    render() {
        const log = this.state.log;
        const logDate = log ? moment.unix(log.date) : null;
        const parser = document.createElement('a');
        parser.href = this.context.baseUrl;

        return (
            <Spin spinning={this.state.loading}>
                <Layout.Content style={{background: '#fff', minHeight: '500px'}}>
                    <Link to={parser.pathname}>
                        <Button icon="left" style={{margin: '10px 10px 0px'}}>Back</Button>
                    </Link>
                    { log ? (
                        <table className="log-detail">
                            <tbody>
                            <tr>
                                <td>Message</td>
                                <td>
                                    <strong>{log.message}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td>Date</td>
                                <td>
                                    {logDate.format('DD.MM.YYYY h:mm:ss')} - {logDate.fromNow()}
                                </td>
                            </tr>
                            <tr>
                                <td>Level</td>
                                <td>
                                    <LogLevelTag level={log.level}></LogLevelTag>
                                </td>
                            </tr>
                            <tr>
                                <td>Channel</td>
                                <td>{log.channel}</td>
                            </tr>
                            <tr>
                                <td>POST</td>
                                <td>
                                    <TreeObject item={log.postData}></TreeObject>
                                </td>
                            </tr>
                            <tr>
                                <td>GET</td>
                                <td>
                                    <TreeObject item={log.getData}></TreeObject>
                                </td>
                            </tr>
                            <tr>
                                <td>Server</td>
                                <td>
                                    <TreeObject item={log.serverData}></TreeObject>
                                </td>
                            </tr>
                            <tr>
                                <td>Context</td>
                                <td>
                                    <TreeObject item={log.context}></TreeObject>
                                </td>
                            </tr>
                            <tr>
                                <td>Extra</td>
                                <td>
                                    <TreeObject item={log.extra}></TreeObject>
                                </td>
                            </tr>
                            <tr>
                                <td>Body</td>
                                <td>{log.bodyData}</td>
                            </tr>
                            </tbody>
                        </table>
                    ) : null
                    }
                </Layout.Content>
            </Spin>
        )
    }
}

LogDetail.contextTypes = {
    baseUrl: React.PropTypes.string,
    serverUrl: React.PropTypes.string
};