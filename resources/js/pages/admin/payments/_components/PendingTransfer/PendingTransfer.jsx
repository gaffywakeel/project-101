import React, {useMemo} from "react";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {route} from "@/services/Http";
import {Card, Stack, Typography} from "@mui/material";
import TrapScrollBox from "@/components/TrapScrollBox";
import AsyncTable from "@/components/AsyncTable";
import ActionBar from "./ActionBar";
import TransferView from "./TransferView";
import AccountBalanceWalletIcon from "@mui/icons-material/AccountBalanceWallet";
import {useCountries} from "@/hooks/global";
import DateCell from "@/components/TableCells/DateCell";
import UserCell from "@/components/TableCells/UserCell";

const messages = defineMessages({
    user: {defaultMessage: "User"},
    note: {defaultMessage: "Note"},
    value: {defaultMessage: "Value"},
    type: {defaultMessage: "Type"},
    action: {defaultMessage: "Action"},
    date: {defaultMessage: "Date"}
});

const PendingTransfer = ({type}) => {
    const intl = useIntl();
    const {countries} = useCountries();

    const columns = useMemo(
        () => [
            {
                field: "account",
                width: 200,
                headerName: intl.formatMessage(messages.user),
                renderCell: ({value: account}) => (
                    <UserCell user={account.user} />
                )
            },
            {
                field: "type",
                width: 70,
                headerName: intl.formatMessage(messages.type),
                align: "center",
                renderCell: ({value}) => {
                    switch (value) {
                        case "receive":
                            return (
                                <AccountBalanceWalletIcon
                                    color="primary"
                                    fontSize="large"
                                />
                            );
                        case "send":
                            return (
                                <AccountBalanceWalletIcon
                                    color="error"
                                    fontSize="large"
                                />
                            );
                    }
                }
            },
            {
                field: "value",
                type: "number",
                minWidth: 100,
                flex: 0.5,
                headerName: intl.formatMessage(messages.value),
                filterable: true,
                renderCell: ({row}) => {
                    const negative = row.type === "send";

                    const countrySearch = countries.find((c) => {
                        return c.code === row.transfer_country;
                    });

                    return (
                        <Stack sx={{minWidth: 0}}>
                            <Typography
                                sx={{whiteSpace: "nowrap"}}
                                variant="body2">
                                {negative && "-"}
                                {row.formatted_value}
                            </Typography>

                            {countrySearch && (
                                <Typography
                                    variant="caption"
                                    sx={{color: "text.secondary"}}
                                    noWrap>
                                    {countrySearch.name}
                                </Typography>
                            )}
                        </Stack>
                    );
                }
            },
            {
                field: "note",
                minWidth: 200,
                flex: 1,
                headerName: intl.formatMessage(messages.note),
                filterable: true,
                renderCell: ({row}) => {
                    return (
                        <Stack sx={{minWidth: 0}}>
                            <Typography variant="body2" noWrap>
                                <FormattedMessage
                                    defaultMessage="Ref: {reference}"
                                    values={{reference: row.account.reference}}
                                />
                            </Typography>

                            <Typography
                                variant="caption"
                                sx={{color: "text.secondary"}}
                                noWrap>
                                {row.transfer_note}
                            </Typography>
                        </Stack>
                    );
                }
            },
            {
                field: "created_at",
                headerName: intl.formatMessage(messages.date),
                width: 150,
                renderCell: ({value}) => <DateCell value={value} />
            },
            {
                field: "action",
                minWidth: 100,
                flex: 0.5,
                headerName: intl.formatMessage(messages.action),
                align: "right",
                headerAlign: "right",
                renderCell: ({row: transaction}) => {
                    return (
                        <Stack direction="row" spacing={1}>
                            <TransferView transaction={transaction} />
                        </Stack>
                    );
                }
            }
        ],
        [intl, countries]
    );

    // prettier-ignore
    const url = useMemo(() => {
        return route("admin.payment-transaction.paginate", {status: "pending-transfer", type});
    }, [type]);

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

export default PendingTransfer;
