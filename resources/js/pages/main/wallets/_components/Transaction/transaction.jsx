import React, {useEffect, useMemo, useRef} from "react";
import {defineMessages, useIntl} from "react-intl";
import AsyncTable from "@/components/AsyncTable";
import {route} from "@/services/Http";
import {useActiveWalletAccount} from "@/hooks/accounts";
import {usePrivateBroadcast} from "@/services/Broadcast";
import {fetchWalletAccounts} from "@/redux/slices/wallet";
import {useDispatch} from "react-redux";
import {useAuth} from "@/models/Auth";
import {Card, CardHeader} from "@mui/material";
import TrapScrollBox from "@/components/TrapScrollBox";
import CompactDateCell from "@/components/TableCells/CompactDateCell";
import DescriptionCell from "@/components/TableCells/WalletTable/DescriptionCell";
import StatusCell from "@/components/TableCells/WalletTable/StatusCell";
import ValueCell from "@/components/TableCells/WalletTable/ValueCell";
import CoinCell from "@/components/TableCells/CoinCell";

const messages = defineMessages({
    transactions: {defaultMessage: "Transactions"},
    title: {defaultMessage: "Title"},
    description: {defaultMessage: "Description"},
    date: {defaultMessage: "Date"},
    hash: {defaultMessage: "Hash"},
    transactionHash: {defaultMessage: "Transaction Hash"},
    coin: {defaultMessage: "Coin"},
    balance: {defaultMessage: "Balance"},
    status: {defaultMessage: "Status"},
    available: {defaultMessage: "Available"},
    value: {defaultMessage: "Value"}
});

const Transaction = () => {
    const tableRef = useRef();
    const dispatch = useDispatch();
    const auth = useAuth();
    const broadcast = usePrivateBroadcast("App.Models.User." + auth.user.id);
    const intl = useIntl();
    const {account} = useActiveWalletAccount();

    useEffect(() => {
        const channel = "TransferRecordSaved";

        const handler = () => {
            dispatch(fetchWalletAccounts());
            tableRef.current.fetchData();
        };

        broadcast.listen(channel, handler);

        return () => {
            broadcast.stopListening(channel, handler);
        };
    }, [broadcast, dispatch]);

    const columns = useMemo(() => {
        return [
            {
                field: "created_at",
                type: "dateTime",
                width: 70,
                headerName: intl.formatMessage(messages.date),
                filterable: true,
                renderCell: ({value}) => <CompactDateCell value={value} />
            },
            {
                field: "type",
                width: 70,
                headerName: intl.formatMessage(messages.status),
                align: "center",
                renderCell: ({row: transaction}) => (
                    <StatusCell transaction={transaction} />
                )
            },
            {
                field: "coin",
                width: 70,
                headerName: intl.formatMessage(messages.coin),
                align: "center",
                renderCell: ({value}) => <CoinCell value={value} />
            },
            {
                field: "value",
                headerName: intl.formatMessage(messages.value),
                type: "number",
                minWidth: 100,
                flex: 0.5,
                filterable: true,
                renderCell: ({row: transaction}) => (
                    <ValueCell transaction={transaction} />
                )
            },
            {
                field: "description",
                minWidth: 200,
                flex: 1,
                headerName: intl.formatMessage(messages.description),
                filterable: true,
                renderCell: ({row: transaction}) => (
                    <DescriptionCell transaction={transaction} />
                )
            }
        ];
    }, [intl]);

    const tableUrl = useMemo(() => {
        return account.isNotEmpty()
            ? route("transfer-records.index", {account: account.get("id")})
            : route("transfer-records.index");
    }, [account]);

    return (
        <Card>
            <CardHeader title={intl.formatMessage(messages.transactions)} />

            <TrapScrollBox>
                <AsyncTable ref={tableRef} columns={columns} url={tableUrl} />
            </TrapScrollBox>
        </Card>
    );
};

export default Transaction;
