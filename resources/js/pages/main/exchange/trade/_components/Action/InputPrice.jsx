import React, {useCallback, useMemo} from "react";
import {isEmpty, isNumber} from "lodash";
import {InputAdornment, Link, Stack} from "@mui/material";
import {TextField} from "@/components/Form";
import {usePaymentAccount} from "@/hooks/accounts";
import {FormattedMessage} from "react-intl";
import {Link as RouterLink} from "react-router-dom";
import CurrencyIcon from "@/components/CurrencyIcon";

const InputPrice = ({value, onChange, onBlur, fee}) => {
    const {account} = usePaymentAccount();

    const updateAmount = useCallback(
        (e) => onChange?.(value?.clone(e.target.value, "price")),
        [value, onChange]
    );

    const helperText = !account.isEmpty() && (
        <Stack
            direction="row"
            justifyContent="space-between"
            component="span"
            spacing={1}>
            <Stack
                direction="row"
                divider={<span>&bull;</span>}
                component="span"
                spacing={1}>
                <span>
                    <FormattedMessage
                        defaultMessage="Available: {available}"
                        values={{available: account.available}}
                    />
                </span>
                {isNumber(fee) && (
                    <span>
                        <FormattedMessage
                            defaultMessage="Fee: {fee}"
                            values={{fee}}
                        />
                    </span>
                )}
            </Stack>

            <Link
                component={RouterLink}
                to="/main/user/account"
                state={{tab: "preferences"}}
                underline="none">
                <FormattedMessage defaultMessage="Change" />
            </Link>
        </Stack>
    );

    const content = useMemo(() => {
        return value?.scale === "price" ? value.amount : value?.priceValue;
    }, [value]);

    return (
        <TextField
            fullWidth={true}
            disabled={isEmpty(value)}
            value={content}
            onChange={updateAmount}
            InputLabelProps={{shrink: true}}
            helperText={helperText}
            type="number"
            onBlur={onBlur}
            InputProps={{
                endAdornment: (
                    <InputAdornment position="end">
                        <CurrencyIcon symbol={account.symbol} />
                    </InputAdornment>
                )
            }}
        />
    );
};

export default InputPrice;
