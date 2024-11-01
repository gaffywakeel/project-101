import React, {useMemo} from "react";
import {defineMessages, useIntl} from "react-intl";
import {route} from "@/services/Http";
import AsyncTable from "@/components/AsyncTable";
import TrapScrollBox from "@/components/TrapScrollBox";
import ActionBar from "./ActionBar";
import BankDelete from "./BankDelete";
import BankEdit from "./BankEdit";
import {Card, Stack} from "@mui/material";
import BankLogo from "@/components/BankLogo";
import {map} from "lodash";

const messages = defineMessages({
    name: {defaultMessage: "Bank"},
    logo: {defaultMessage: "Logo"},
    countries: {defaultMessage: "Countries"},
    code: {defaultMessage: "Code"},
    action: {defaultMessage: "Action"},
    banks: {defaultMessage: "Banks"}
});

const Banks = () => {
    const intl = useIntl();

    const columns = useMemo(
        () => [
            {
                field: "logo",
                width: 70,
                headerName: intl.formatMessage(messages.logo),
                align: "center",
                renderCell: ({value}) => <BankLogo src={value} />
            },
            {
                field: "name",
                minWidth: 150,
                flex: 1,
                headerName: intl.formatMessage(messages.name)
            },
            {
                field: "operating_countries",
                minWidth: 150,
                flex: 1,
                headerName: intl.formatMessage(messages.countries),
                renderCell: ({value: countries}) => {
                    return map(countries, "name").join(", ");
                }
            },
            {
                field: "action",
                minWidth: 100,
                flex: 0.5,
                headerAlign: "right",
                headerName: intl.formatMessage(messages.action),
                align: "right",
                renderCell: ({row: bank}) => {
                    return (
                        <Stack direction="row" spacing={1}>
                            <BankDelete bank={bank} />
                            <BankEdit bank={bank} />
                        </Stack>
                    );
                }
            }
        ],
        [intl]
    );

    const url = route("admin.bank.paginate");

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

export default Banks;
