import React, {useMemo} from "react";
import ItemSummary from "../ItemSummary";
import {FormattedMessage} from "react-intl";
import Form from "@/components/Form";

const Payment = () => {
    const payment = Form.useWatch("payment");
    const paymentMethod = Form.useWatch("payment_method");

    const content = useMemo(() => {
        switch (payment) {
            case "bank_account":
                return <FormattedMessage defaultMessage="Bank Account" />;
            case "payment_method":
                return paymentMethod?.name;
        }
    }, [payment, paymentMethod]);

    return (
        <ItemSummary
            title={<FormattedMessage defaultMessage="Payment" />}
            content={content}
        />
    );
};

export default Payment;
