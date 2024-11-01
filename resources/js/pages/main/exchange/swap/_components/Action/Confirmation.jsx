import React, {
    Fragment,
    useCallback,
    useContext,
    useEffect,
    useMemo
} from "react";
import {FormContext} from "@/components/Form/contexts";
import {useTokenField} from "@/hooks/useTokenField";
import {Box, Button, Stack, Typography} from "@mui/material";
import TwoFactorField from "@/components/TwoFactorField";
import ModalActions from "@/components/ModalActions";
import {FormattedMessage} from "react-intl";
import ModalContent from "@/components/ModalContent";
import {useWalletAccounts} from "@/hooks/accounts";
import WalletAccount from "@/models/WalletAccount";
import ArrowForwardIcon from "@mui/icons-material/ArrowForward";
import Alert from "@/components/Alert";
import CoinIcon from "@/components/CoinIcon";

const Confirmation = ({summary, closeModal}) => {
    const field = useTokenField();
    const {form} = useContext(FormContext);
    const {accounts} = useWalletAccounts();

    useEffect(() => {
        form.resetFields([field]);
    }, [form, field]);

    const getAccount = useCallback(
        (name) => {
            const id = form.getFieldValue(name);
            const account = accounts.find((o) => o.id === id);
            return WalletAccount.use(account);
        },
        [form, accounts]
    );

    const sellAccount = useMemo(() => {
        return getAccount("sell_account");
    }, [getAccount]);

    const buyAccount = useMemo(() => {
        return getAccount("buy_account");
    }, [getAccount]);

    const submit = useCallback(() => {
        form.validateFields([field])
            .then(() => {
                form.submit();
                closeModal();
            })
            .catch(console.debug);
    }, [closeModal, field, form]);

    const sufficient = useMemo(() => {
        return sellAccount.available >= summary.sell_value;
    }, [sellAccount, summary]);

    return (
        <Fragment>
            <ModalContent spacing={5}>
                <Stack
                    alignItems="center"
                    direction={{xs: "column", sm: "row"}}
                    spacing={{xs: 4, sm: 2}}>
                    <TradeValue
                        account={sellAccount}
                        price={summary.formatted_sell_value_price}
                        value={summary.sell_value}
                    />

                    <Box sx={{flexShrink: 0}}>
                        <ArrowForwardIcon />
                    </Box>

                    <TradeValue
                        account={buyAccount}
                        price={summary.formatted_buy_value_price}
                        value={summary.buy_value}
                    />
                </Stack>

                {!sufficient ? (
                    <Alert severity="error">
                        <FormattedMessage
                            defaultMessage="Your balance of {balance} {symbol} is not sufficient."
                            values={{
                                balance: <b>{sellAccount.available}</b>,
                                symbol: sellAccount.wallet.coin.symbol
                            }}
                        />
                    </Alert>
                ) : (
                    <TwoFactorField />
                )}
            </ModalContent>

            <ModalActions>
                <Button
                    fullWidth
                    variant="contained"
                    disabled={!sufficient}
                    onClick={submit}
                    size="large">
                    <FormattedMessage defaultMessage="Convert" />
                </Button>
            </ModalActions>
        </Fragment>
    );
};

const TradeValue = ({account, value, price}) => {
    return (
        <Stack
            spacing={1}
            alignItems="center"
            sx={{
                flexGrow: 1,
                flexBasis: 0,
                minWidth: 0,
                width: "100%"
            }}>
            <CoinIcon sx={{p: 1}} coin={account.wallet.coin} size={50} />

            <Typography
                variant="subtitle1"
                sx={{lineHeight: 1, maxWidth: 1}}
                color="text.primary"
                noWrap>
                {account.formatValue(value)}
            </Typography>

            <Typography
                variant="caption"
                sx={{lineHeight: 1, maxWidth: 1}}
                color="text.secondary"
                noWrap>
                {price}
            </Typography>
        </Stack>
    );
};

export default Confirmation;
