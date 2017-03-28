import React, {Component} from 'react';
import {Tree} from 'antd';

export default class TreeObject extends Component {

    render() {
        let obj = this.props.item;

        if (Object.keys(obj).length === 0) {
            return <span>-</span>
        }

        const randomKey = Math.floor((Math.random() * 1000000) + 1);

        return (<Tree key={randomKey}>
                {this.eachRecursive(obj)}
            </Tree>
        )
    }

    eachRecursive(obj) {
        let nodes = [];

        for (let k in obj) {
            const randomKey = Math.floor((Math.random() * 1000000) + 1);
            if (typeof obj[k] == "object" && obj[k] !== null) {
                nodes.push(
                    <Tree.TreeNode title={k} key={randomKey}>
                        {this.eachRecursive(obj[k])}
                    </Tree.TreeNode>
                );
            }
            else {
                let title = (<span><strong>{k}</strong>: {obj[k]}</span>)
                nodes.push(<Tree.TreeNode title={title} key={randomKey}/>);
            }
        }

        return nodes
    }
}
