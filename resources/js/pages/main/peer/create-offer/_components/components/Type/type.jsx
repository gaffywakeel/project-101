import React, {useMemo} from "react";
import {FormattedMessage} from "react-intl";
import Form from "@/components/Form";
import ItemSummary from "../ItemSummary";

const Type = () => {
    const type = Form.useWatch("type");

    const content = useMemo(() => {
        switch (type) {
            case "sell":
                return <FormattedMessage defaultMessage="Sell" />;
            case "buy":
                return <FormattedMessage defaultMessage="Buy" />;
        }
    }, [type]);

    return (
        <ItemSummary
            title={<FormattedMessage defaultMessage="Offer Type" />}
            content={content}
        />
    );
};

export default Type;
