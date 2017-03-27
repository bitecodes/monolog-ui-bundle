import React from 'react';
import {Tag} from 'antd';
import {LOG_LEVELS} from './_log-levels';

export default ({level}) => {
    const l = LOG_LEVELS.find((l) => l.value === level);

    return (
        <Tag color={l.color}>{l.text}</Tag>
    )
}