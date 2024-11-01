import React, {useEffect, useState} from "react";
import {defineMessages, useIntl} from "react-intl";
import {errorHandler, route, useRequest} from "@/services/Http";
import Form, {AutoComplete} from "@/components/Form";

const messages = defineMessages({
    paymentMethod: {defaultMessage: "Payment Method"},
    bankAccount: {defaultMessage: "Bank Account"}
});

const PaymentField = () => {
    const type = Form.useWatch("type");
    const payment = Form.useWatch("payment");

    if (payment !== "bank_account") {
        return <PaymentMethod />;
    }

    return type === "sell" && <BankAccount />;
};

const BankAccount = () => {
    const intl = useIntl();
    const [accounts, setAccounts] = useState([]);
    const [request, loading] = useRequest();

    useEffect(() => {
        request
            .get(route("bank-account.all"))
            .then(({data}) => setAccounts(data))
            .catch(errorHandler());
    }, [request]);

    const getName = (option) => {
        return `${option.bank_name} (${option.number})`;
    };

    return (
        <Form.Item
            name="bank_account"
            label={intl.formatMessage(messages.bankAccount)}
            rules={[{required: true}]}>
            <AutoComplete
                options={accounts}
                isOptionEqualToValue={(option, value) => option.id === value.id}
                getOptionLabel={(option) => getName(option)}
                loading={loading}
                renderOption={(props, option) => (
                    <li {...props} key={option.id}>
                        {getName(option)}
                    </li>
                )}
            />
        </Form.Item>
    );
};

const PaymentMethod = () => {
    const intl = useIntl();
    const [methods, setMethods] = useState([]);
    const [request, loading] = useRequest();

    useEffect(() => {
        request
            .get(route("peer-payment-method.all"))
            .then(({data}) => setMethods(data))
            .catch(errorHandler());
    }, [request]);

    return (
        <Form.Item
            name="payment_method"
            label={intl.formatMessage(messages.paymentMethod)}
            rules={[{required: true}]}>
            <AutoComplete
                options={methods}
                getOptionLabel={(option) => option.name}
                isOptionEqualToValue={(option, value) => option.id === value.id}
                groupBy={(option) => option.category.name}
                loading={loading}
                renderOption={(props, option) => (
                    <li {...props} key={option.id}>
                        {option.name}
                    </li>
                )}
            />
        </Form.Item>
    );
};

export default PaymentField;
