import React, {useMemo} from "react";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {isEmpty} from "lodash";
import {route} from "@/services/Http";
import {Card, Stack, Tooltip, Typography} from "@mui/material";
import ActionBar from "./ActionBar";
import WalletDelete from "./WalletDelete";
import WalletMenu from "./WalletMenu";
import TrapScrollBox from "@/components/TrapScrollBox";
import AsyncTable from "@/components/AsyncTable";
import Coin from "@/models/Coin";
import CoinCell from "@/components/TableCells/CoinCell";
import WalletHelp from "./WalletHelp";

const messages = defineMessages({
    name: {defaultMessage: "Name"},
    accounts: {defaultMessage: "Accounts"},
    confirmations: {defaultMessage: "Confirmations"},
    balance: {defaultMessage: "Balance"},
    wallet: {defaultMessage: "Wallet"},
    action: {defaultMessage: "Action"},
    onTrade: {defaultMessage: "On Trade"}
});

const Wallets = () => {
    const intl = useIntl();

    const columns = useMemo(
        () => [
            {
                field: "id",
                minWidth: 150,
                flex: 1,
                headerName: intl.formatMessage(messages.wallet),
                renderCell: ({row}) => {
                    const coin = Coin.use(row.coin);
                    return (
                        <Stack direction="row" sx={{minWidth: 0}} spacing={2}>
                            <CoinCell value={row.coin} />

                            <Stack sx={{minWidth: 0}}>
                                <Typography variant="body2" noWrap>
                                    {coin.name}
                                </Typography>

                                <Typography
                                    variant="caption"
                                    sx={{color: "text.secondary"}}
                                    noWrap>
                                    {coin.formatted_price}
                                </Typography>
                            </Stack>
                        </Stack>
                    );
                }
            },
            {
                field: "accounts_count",
                minWidth: 100,
                flex: 0.5,
                headerName: intl.formatMessage(messages.accounts)
            },
            {
                field: "min_conf",
                minWidth: 100,
                flex: 0.5,
                headerName: intl.formatMessage(messages.confirmations)
            },
            {
                field: "balance",
                minWidth: 150,
                flex: 1,
                headerName: intl.formatMessage(messages.balance),
                renderCell: ({row}) => {
                    const statistic = row.statistic;

                    return isEmpty(statistic) ? (
                        <Typography variant="caption">
                            <FormattedMessage defaultMessage="Unavailable" />
                        </Typography>
                    ) : (
                        <Tooltip title={statistic.balance}>
                            <Stack>
                                <Typography variant="body2">
                                    {statistic.balance}
                                </Typography>

                                <Typography
                                    sx={{color: "text.secondary"}}
                                    variant="caption">
                                    {statistic.formatted_balance_price}
                                </Typography>
                            </Stack>
                        </Tooltip>
                    );
                }
            },
            {
                field: "balance_on_trade",
                minWidth: 150,
                flex: 1,
                headerName: intl.formatMessage(messages.onTrade),
                renderCell: ({row}) => {
                    const statistic = row.statistic;

                    return isEmpty(statistic) ? (
                        <Typography variant="caption">
                            <FormattedMessage defaultMessage="Unavailable" />
                        </Typography>
                    ) : (
                        <Tooltip title={statistic.balance}>
                            <Stack>
                                <Typography variant="body2">
                                    {statistic.balance_on_trade}
                                </Typography>

                                <Typography
                                    sx={{color: "text.secondary"}}
                                    variant="caption">
                                    {statistic.formatted_balance_on_trade_price}
                                </Typography>
                            </Stack>
                        </Tooltip>
                    );
                }
            },
            {
                field: "action",
                minWidth: 150,
                flex: 0.5,
                headerAlign: "right",
                headerName: intl.formatMessage(messages.action),
                align: "right",
                renderCell: ({row: wallet}) => {
                    return (
                        <Stack direction="row" spacing={1}>
                            <WalletHelp wallet={wallet} />
                            <WalletDelete wallet={wallet} />
                            <WalletMenu wallet={wallet} />
                        </Stack>
                    );
                }
            }
        ],
        [intl]
    );

    const url = route("wallets.index");

    return (
        <Card>
            <TrapScrollBox>
                <AsyncTable
                    columns={columns}
                    components={{Toolbar: ActionBar}}
                    url={url}
                />
            </TrapScrollBox>
        </Card>
    );
};

export default Wallets;
