import React from "react";
import Form from "@/components/Form";
import ItemSummary from "../ItemSummary";
import {FormattedMessage} from "react-intl";

const MaxAmount = () => {
    const content = Form.useWatch("max_amount");
    const currency = Form.useWatch("currency");

    return (
        <ItemSummary
            title={
                <FormattedMessage
                    defaultMessage="Max Amount ({currency})"
                    values={{currency}}
                />
            }
            content={content}
        />
    );
};

export default MaxAmount;
