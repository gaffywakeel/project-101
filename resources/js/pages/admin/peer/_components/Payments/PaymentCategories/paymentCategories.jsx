import React, {useMemo} from "react";
import TrapScrollBox from "@/components/TrapScrollBox";
import AsyncTable from "@/components/AsyncTable";
import {Card, Stack} from "@mui/material";
import {route} from "@/services/Http";
import {defineMessages, useIntl} from "react-intl";
import ActionBar from "./ActionBar";
import CategoryDelete from "./CategoryDelete";
import CategoryEdit from "./CategoryEdit";

const messages = defineMessages({
    name: {defaultMessage: "Category"},
    description: {defaultMessage: "Description"},
    action: {defaultMessage: "Action"},
    methods: {defaultMessage: "Methods"}
});

const PaymentCategories = () => {
    const intl = useIntl();

    const columns = useMemo(
        () => [
            {
                field: "name",
                minWidth: 100,
                flex: 0.5,
                headerName: intl.formatMessage(messages.name)
            },
            {
                field: "description",
                minWidth: 200,
                flex: 1,
                headerName: intl.formatMessage(messages.description)
            },
            {
                field: "methods_count",
                width: 100,
                headerName: intl.formatMessage(messages.methods)
            },
            {
                field: "action",
                minWidth: 100,
                flex: 0.5,
                headerName: intl.formatMessage(messages.action),
                align: "right",
                renderCell: ({row: category}) => {
                    return (
                        <Stack direction="row" spacing={1}>
                            <CategoryDelete category={category} />
                            <CategoryEdit category={category} />
                        </Stack>
                    );
                }
            }
        ],
        [intl]
    );

    const url = route("admin.peer-payment-category.paginate");

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

export default PaymentCategories;
