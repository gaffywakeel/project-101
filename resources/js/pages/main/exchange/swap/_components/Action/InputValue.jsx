import React, {useMemo} from "react";
import Form, {TextField} from "@/components/Form";
import {FormattedMessage} from "react-intl";
import {useWalletAccounts} from "@/hooks/accounts";

const InputValue = ({value, onChange, accountName, ...otherProps}) => {
    const {accounts} = useWalletAccounts();
    const accountId = Form.useWatch(accountName);

    const account = useMemo(() => {
        return accounts.find((o) => o.id === accountId);
    }, [accounts, accountId]);

    return (
        <TextField
            value={value}
            onChange={onChange}
            type="number"
            disabled={!account}
            helperText={<Available account={account} />}
            InputLabelProps={{shrink: true}}
            fullWidth
            {...otherProps}
        />
    );
};

const Available = ({account}) => {
    if (!account) {
        return null;
    }

    return (
        <FormattedMessage
            defaultMessage="Available: {available}"
            values={{available: account.available}}
        />
    );
};

export default InputValue;
