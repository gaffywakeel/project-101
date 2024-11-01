import React, {useMemo} from "react";
import {defineMessages, useIntl} from "react-intl";
import {route} from "@/services/Http";
import {Card, Stack} from "@mui/material";
import ActionBar from "./ActionBar";
import CountryDelete from "./CountryDelete";
import TrapScrollBox from "@/components/TrapScrollBox";
import AsyncTable from "@/components/AsyncTable";

const messages = defineMessages({
    name: {defaultMessage: "Country"},
    code: {defaultMessage: "Code"},
    action: {defaultMessage: "Action"},
    banks: {defaultMessage: "Banks"}
});

const Countries = () => {
    const intl = useIntl();

    const columns = useMemo(
        () => [
            {
                field: "code",
                width: 70,
                headerName: intl.formatMessage(messages.code)
            },
            {
                field: "name",
                minWidth: 150,
                flex: 1,
                headerName: intl.formatMessage(messages.name)
            },
            {
                field: "banks_count",
                width: 100,
                headerName: intl.formatMessage(messages.banks)
            },
            {
                field: "action",
                minWidth: 100,
                flex: 0.5,
                headerAlign: "right",
                headerName: intl.formatMessage(messages.action),
                align: "right",
                renderCell: ({row: country}) => {
                    return (
                        <Stack direction="row" spacing={1}>
                            <CountryDelete country={country} />
                        </Stack>
                    );
                }
            }
        ],
        [intl]
    );

    const url = route("admin.operating-country.paginate");

    return (
        <Card>
            <TrapScrollBox>
                <AsyncTable
                    columns={columns}
                    components={{Toolbar: ActionBar}}
                    getRowId={(row) => row.code}
                    url={url}
                />
            </TrapScrollBox>
        </Card>
    );
};

export default Countries;
