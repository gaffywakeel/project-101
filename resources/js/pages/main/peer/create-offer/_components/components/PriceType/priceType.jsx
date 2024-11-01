import React, {useMemo} from "react";
import Form from "@/components/Form";
import ItemSummary from "../ItemSummary";
import {FormattedMessage} from "react-intl";

const PriceType = () => {
    const priceType = Form.useWatch("price_type");

    const content = useMemo(() => {
        switch (priceType) {
            case "percent":
                return <FormattedMessage defaultMessage="Percent" />;
            case "fixed":
                return <FormattedMessage defaultMessage="Fixed" />;
        }
    }, [priceType]);

    return (
        <ItemSummary
            title={<FormattedMessage defaultMessage="Price Type" />}
            content={content}
        />
    );
};

export default PriceType;
