import React, {useEffect, useMemo, useRef} from "react";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {useAuth} from "@/models/Auth";
import {usePrivateBroadcast} from "@/services/Broadcast";
import CompactDateCell from "@/components/TableCells/CompactDateCell";
import ValueCell from "@/components/TableCells/ExchangeTable/ValueCell";
import SwapStatusCell from "@/components/TableCells/ExchangeTable/SwapStatusCell";
import {Card, CardHeader} from "@mui/material";
import AsyncTable from "@/components/AsyncTable";
import {route} from "@/services/Http";

const messages = defineMessages({
    transactions: {defaultMessage: "Transactions"},
    title: {defaultMessage: "Title"},
    from: {defaultMessage: "From"},
    to: {defaultMessage: "To"},
    date: {defaultMessage: "Date"},
    status: {defaultMessage: "Status"},
    swap: {defaultMessage: "Swap"}
});

const Transaction = () => {
    const auth = useAuth();
    const intl = useIntl();
    const tableRef = useRef();
    const broadcast = usePrivateBroadcast("App.Models.User." + auth.user.id);

    useEffect(() => {
        const handler = () => tableRef.current.fetchData();

        broadcast.listen(".ExchangeSwapCreated", handler);
        broadcast.listen(".ExchangeSwapUpdated", handler);

        return () => {
            broadcast.stopListening(".ExchangeSwapCreated", handler);
            broadcast.stopListening(".ExchangeSwapUpdated", handler);
        };
    }, [broadcast]);

    const columns = useMemo(
        () => [
            {
                field: "created_at",
                minWidth: 70,
                flex: 0.5,
                type: "dateTime",
                filterable: true,
                headerName: intl.formatMessage(messages.date),
                renderCell: ({value}) => <CompactDateCell value={value} />
            },
            {
                field: "sell_value",
                minWidth: 100,
                flex: 1,
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
                minWidth: 100,
                flex: 1,
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
                minWidth: 100,
                flex: 0.8,
                headerName: intl.formatMessage(messages.status),
                renderCell: ({value}) => <SwapStatusCell status={value} />
            }
        ],
        [intl]
    );

    const tableUrl = route("exchange-swaps.paginate");

    return (
        <Card>
            <CardHeader
                title={<FormattedMessage defaultMessage="Transactions" />}
            />

            <AsyncTable ref={tableRef} columns={columns} url={tableUrl} />
        </Card>
    );
};

export default Transaction;
