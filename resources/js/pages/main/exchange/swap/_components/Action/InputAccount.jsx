import React from "react";
import {useWalletAccounts} from "@/hooks/accounts";
import Form, {SelectAdornment} from "@/components/Form";
import {Box, MenuItem, Stack} from "@mui/material";
import CoinIcon from "@/components/CoinIcon";

const InputAccount = ({dependency, ...props}) => {
    const exclusive = Form.useWatch(dependency);
    const {accounts} = useWalletAccounts();

    return (
        <SelectAdornment {...props}>
            {accounts
                .filter((data) => data.id !== exclusive)
                .map((account) => (
                    <MenuItem value={account.id} key={account.id}>
                        <Stack direction="row" alignItems="center">
                            <CoinIcon coin={account.wallet.coin} size={20} />

                            <Box component="span" sx={{ml: 1}}>
                                {account.wallet.coin.symbol}
                            </Box>
                        </Stack>
                    </MenuItem>
                ))}
        </SelectAdornment>
    );
};

export default InputAccount;
