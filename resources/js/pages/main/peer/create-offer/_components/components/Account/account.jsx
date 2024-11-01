import React from "react";
import ItemSummary from "../ItemSummary";
import {FormattedMessage} from "react-intl";
import {useActiveWalletAccount} from "@/hooks/accounts";

const Account = () => {
    const {account} = useActiveWalletAccount();

    if (account.isEmpty()) {
        return null;
    }

    return (
        <ItemSummary
            title={<FormattedMessage defaultMessage="Account" />}
            content={account.wallet.coin.name}
        />
    );
};

export default Account;
