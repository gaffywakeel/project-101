import React, {memo, useCallback} from "react";
import WalletAccount from "@/models/WalletAccount";
import IconBuilder from "../IconBuilder";
import {useActiveWalletAccount, useWalletAccounts} from "@/hooks/accounts";
import {defineMessages, useIntl} from "react-intl";
import {setActiveAccount} from "@/redux/slices/wallet";
import {useDispatch} from "react-redux";
import {styled} from "@mui/material/styles";
import {MenuItem, TextField, Typography} from "@mui/material";

const messages = defineMessages({
    placeholder: {defaultMessage: "Select account"}
});

const GlobalAccountSelect = memo((props) => {
    const {accounts} = useWalletAccounts();
    const {account: activeAccount} = useActiveWalletAccount();
    const intl = useIntl();
    const dispatch = useDispatch();

    const updateAccount = useCallback(
        (e) => {
            dispatch(setActiveAccount(e.target.value));
        },
        [dispatch]
    );

    return (
        <TextField
            size="small"
            fullWidth
            value={activeAccount.id || ""}
            label={intl.formatMessage(messages.placeholder)}
            onChange={updateAccount}
            select
            {...props}>
            {accounts.map((record) => {
                const account = WalletAccount.use(record);
                const icon = account.wallet.coin.svgIcon();

                return (
                    <MenuItem value={account.id} key={account.id}>
                        <CoinWrapper>
                            <IconBuilder sx={{fontSize: "25px"}} icon={icon} />

                            <Typography sx={{ml: 1}} noWrap>
                                {account.wallet.coin.name}
                            </Typography>
                        </CoinWrapper>
                    </MenuItem>
                );
            })}
        </TextField>
    );
});

const CoinWrapper = styled("div")({
    display: "flex",
    alignItems: "center",
    minWidth: 0
});

export default GlobalAccountSelect;
