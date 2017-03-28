import React, {Component} from 'react';
import {Badge, Table, Switch, Row, Col, Popconfirm, message} from 'antd';
import {Link} from 'react-router-dom';
import LogLevelTag from './LogLevelTag';
import {LOG_LEVELS} from './_log-levels';
import moment from "moment";
import {Observable} from "rxjs/bundles/Rx";

export default class LogTable extends Component {

    constructor(props) {
        super(props);

        this.state = {
            pagination: {current: 1},
            filters: {},
            sorter: [],
            isGrouped: true
        }
    }

    triggerSearch() {
        const {pagination, filters, sorter, isGrouped} = this.state;

        this.props.onChange(
            pagination,
            Object.assign(filters, {grouped: isGrouped ? 1 : null}),
            sorter
        );
    }

    onTableChange = (pagination, filters, sorter) => {
        this.setState({pagination, filters, sorter}, this.triggerSearch);
    }

    onGroupChange = (isGrouped) => {
        this.setState({pagination: {current: 1}, isGrouped}, this.triggerSearch);
    }

    confirmDelete = (log) => {
        return () => {
            Observable
                .ajax({
                    url: this.context.serverUrl + log.id,
                    method: 'DELETE',
                    body: {similar: this.state.isGrouped}
                })
                .subscribe(_ => {
                    message.success(this.state.isGrouped ? 'Logs deleted' : 'Log deleted');
                    this.triggerSearch();
                });
        }
    }

    insertCountColumn() {
        return this.state.isGrouped ? [
            {
                title: 'Count',
                dataIndex: 'count',
                key: 'count',
                width: '5%',
                render: (text) => {
                    return (
                        <Badge count={text}
                               overflowCount={999}
                               style={{backgroundColor: '#fff', color: '#999', boxShadow: '0 0 0 1px #d9d9d9 inset'}}/>
                    )
                }
            }
        ] : [];
    }

    render() {
        const columns = [{
            title: 'Level',
            dataIndex: 'level',
            key: 'levels',
            width: '6%',
            filters: LOG_LEVELS,
            render: (level) => {
                return (
                    <div>
                        <LogLevelTag level={level}></LogLevelTag>
                    </div>
                )
            }
        }, ...this.insertCountColumn(), {
            title: this.state.isGrouped ? 'Last seen' : 'Time',
            dataIndex: 'datetime',
            key: 'datetime',
            width: '10%',
            render: (text, record) => {
                const time = moment.unix(record.datetime).fromNow()
                return time;
            }
        }, {
            title: 'Channel',
            dataIndex: 'channel',
            key: 'channels',
            width: '10%',
            filters: this.context.channels.map((channel) => ({key: channel, text: channel, value: channel}))
        }, {
            title: 'Message',
            dataIndex: 'message',
            key: 'message',
        }, {
            title: 'Action',
            key: 'action',
            width: '15%',
            render: (text, record) => {
                const parser = document.createElement('a');
                parser.href = this.context.baseUrl;

                return (
                    <span>
                        <Link to={parser.pathname + record.id}>Details</Link>
                        <span className="ant-divider"/>
                        <Popconfirm
                            title={this.state.isGrouped ? "Delete this and all similar log entries?" : "Delete this log entry?"}
                            onConfirm={this.confirmDelete(record)}
                            okText="Yes"
                            cancelText="No"
                        >
                            <a href="#">Delete</a>
                        </Popconfirm>
                    </span>
                )
            },
        }];

        const pagination = {
            defaultPageSize: 20,
            pageSize: 20,
            total: this.props.total,
            showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} entries`
        };

        return (
            <Table
                dataSource={this.props.items}
                columns={columns}
                rowKey={record => record.id}
                pagination={pagination}
                onChange={this.onTableChange}
                loading={this.props.loading}
                size='middle'
                bordered={true}
                title={() => (
                    <Row>
                        <Col span={8}>
                            <h3>Logs</h3>
                        </Col>
                        <Col span={8} offset={8}>
                            <Switch
                                style={{float: 'right'}}
                                checked={this.state.isGrouped}
                                checkedChildren="Group View"
                                unCheckedChildren="Single View"
                                onChange={this.onGroupChange}
                            />
                        </Col>
                    </Row>
                )}
            />
        )
    }
}

LogTable.contextTypes = {
    channels: React.PropTypes.array,
    baseUrl: React.PropTypes.string,
    serverUrl: React.PropTypes.string,
};