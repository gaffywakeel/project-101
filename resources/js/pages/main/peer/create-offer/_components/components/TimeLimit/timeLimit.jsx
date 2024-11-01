import React from "react";
import ItemSummary from "../ItemSummary";
import {FormattedMessage} from "react-intl";
import Form from "@/components/Form";

const TimeLimit = () => {
    const content = Form.useWatch("time_limit");

    return (
        <ItemSummary
            title={<FormattedMessage defaultMessage="Time Limit" />}
            content={
                <FormattedMessage
                    defaultMessage="{content} minutes"
                    values={{content}}
                />
            }
        />
    );
};

export default TimeLimit;
