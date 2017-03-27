import React, {Component} from 'react';
import {Observable} from "rxjs/bundles/Rx";
import LogTable from './LogTable';
import {BehaviorSubject} from "rxjs/bundles/Rx";

export default class LogList extends Component {

    constructor(props) {
        super(props);

        this.search$ = new BehaviorSubject({grouped: 1});

        this.state = {
            logs: [],
            totalLogs: 0,
            loading: false,
        };
    }

    encodeQueryData(data) {
        let ret = [];
        for (let d in data)
            if (data[d]) {
                ret.push(encodeURIComponent(d) + '=' + encodeURIComponent(data[d]));
            }
        return ret.join('&');
    }

    componentDidMount() {
        this.search$
            .switchMap(searchParams => {
                const url = this.context.serverUrl + '?' + this.encodeQueryData(searchParams);
                this.setState({loading: true});
                return Observable
                    .ajax(url)
                    .map((ajax) => ajax.response)
                    .do(_ => this.setState({loading: false}))
            })
            .subscribe(({data, meta}) => this.setState({logs: data, totalLogs: meta.total}));
    }

    onTableChange = (pagination, filters, sorters) => {
        this.search$.next({
            page: pagination.current,
            ...filters,
        })
    }

    render() {
        return (
            <LogTable
                onChange={this.onTableChange}
                items={this.state.logs}
                total={this.state.totalLogs}
                loading={this.state.loading}
            ></LogTable>
        )
    }
}

LogList.contextTypes = {
    serverUrl: React.PropTypes.string
};