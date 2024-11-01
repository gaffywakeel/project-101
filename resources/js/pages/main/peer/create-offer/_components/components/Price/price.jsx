import React, {useMemo} from "react";
import Form from "@/components/Form";
import {FormattedMessage} from "react-intl";
import {round} from "lodash";
import {useActiveWalletAccount} from "@/hooks/accounts";
import TotalSummary from "../TotalSummary";

const Price = () => {
    const {account} = useActiveWalletAccount();

    const priceType = Form.useWatch("price_type");
    const percentPrice = Form.useWatch("percent_price");
    const fixedPrice = Form.useWatch("fixed_price");
    const currency = Form.useWatch("currency");

    const content = useMemo(() => {
        if (priceType !== "percent") return fixedPrice;
        const value = (percentPrice * account.price) / 100;
        return round(value, account.wallet.coin.currency_precision);
    }, [priceType, fixedPrice, percentPrice, account]);

    if (account.isEmpty()) {
        return null;
    }

    return (
        <TotalSummary
            title={
                <FormattedMessage
                    defaultMessage="Price ({currency})"
                    values={{currency}}
                />
            }
            content={content}
        />
    );
};

export default Price;
