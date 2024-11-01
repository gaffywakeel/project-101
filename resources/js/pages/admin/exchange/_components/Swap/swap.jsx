import React, {useMemo} from "react";
import {defineMessages, useIntl} from "react-intl";
import UserCell from "@/components/TableCells/UserCell";
import ValueCell from "@/components/TableCells/ExchangeTable/ValueCell";
import SwapStatusCell from "@/components/TableCells/ExchangeTable/SwapStatusCell";
import {Card, Stack} from "@mui/material";
import SwapApprove from "./SwapApprove";
import SwapCancel from "./SwapCancel";
import DateCell from "@/components/TableCells/DateCell";
import AsyncTable from "@/components/AsyncTable";
import TrapScrollBox from "@/components/TrapScrollBox";
import {route} from "@/services/Http";
import ActionBar from "./ActionBar";

const messages = defineMessages({
    user: {defaultMessage: "User"},
    created: {defaultMessage: "Created"},
    from: {defaultMessage: "From"},
    to: {defaultMessage: "To"},
    date: {defaultMessage: "Date"},
    status: {defaultMessage: "Status"},
    action: {defaultMessage: "Action"},
    swap: {defaultMessage: "Swap"}
});

const Swap = () => {
    const intl = useIntl();

    const columns = useMemo(
        () => [
            {
                field: "sell_wallet_account",
                flex: 0.5,
                minWidth: 200,
                headerName: intl.formatMessage(messages.user),
                renderCell: ({value}) => <UserCell user={value.user} />
            },
            {
                field: "sell_value",
                flex: 0.5,
                minWidth: 100,
                headerName: intl.formatMessage(messages.from),
                renderCell: ({row: swap}) => (
                    <ValueCell
                        value={swap.sell_value}
                        price={swap.formatted_sell_value_price}
                        coin={swap.sell_wallet.coin}
                    />
                )
            },
            {
                field: "buy_value",
                flex: 0.5,
                minWidth: 100,
                headerName: intl.formatMessage(messages.to),
                renderCell: ({row: swap}) => (
                    <ValueCell
                        value={swap.buy_value}
                        price={swap.formatted_buy_value_price}
                        coin={swap.buy_wallet.coin}
                    />
                )
            },
            {
                field: "status",
                flex: 0.5,
                minWidth: 100,
                headerName: intl.formatMessage(messages.status),
                renderCell: ({value}) => <SwapStatusCell status={value} />
            },
            {
                field: "created_at",
                flex: 0.5,
                minWidth: 150,
                headerName: intl.formatMessage(messages.created),
                renderCell: ({value}) => <DateCell value={value} />
            },
            {
                field: "action",
                flex: 0.5,
                minWidth: 100,
                align: "right",
                headerName: intl.formatMessage(messages.action),
                headerAlign: "right",
                renderCell: ({row: swap}) => (
                    <Stack direction="row" spacing={1}>
                        <SwapCancel swap={swap} />
                        <SwapApprove swap={swap} />
                    </Stack>
                )
            }
        ],
        [intl]
    );

    const tableUrl = route("admin.exchange-swaps.paginate");

    return (
        <Card>
            <TrapScrollBox>
                <AsyncTable
                    columns={columns}
                    components={{Toolbar: ActionBar}}
                    url={tableUrl}
                />
            </TrapScrollBox>
        </Card>
    );
};

export default Swap;
