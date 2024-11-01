import React from "react";
import ItemSummary from "../ItemSummary";
import {FormattedMessage} from "react-intl";
import Form from "@/components/Form";

const MinAmount = () => {
    const content = Form.useWatch("min_amount");
    const currency = Form.useWatch("currency");

    return (
        <ItemSummary
            title={
                <FormattedMessage
                    defaultMessage="Min Amount ({currency})"
                    values={{currency}}
                />
            }
            content={content}
        />
    );
};

export default MinAmount;
