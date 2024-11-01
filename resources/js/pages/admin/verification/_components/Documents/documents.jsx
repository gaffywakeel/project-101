import React, {useMemo} from "react";
import {defineMessages, useIntl} from "react-intl";
import {route} from "@/services/Http";
import {Card, Stack} from "@mui/material";
import ActionBar from "./ActionBar";
import TrapScrollBox from "@/components/TrapScrollBox";
import AsyncTable from "@/components/AsyncTable";
import DocumentDelete from "./DocumentDelete";
import DocumentEdit from "./DocumentEdit";

const messages = defineMessages({
    name: {defaultMessage: "Name"},
    status: {defaultMessage: "Status"},
    description: {defaultMessage: "Description"},
    action: {defaultMessage: "Action"},
    approved: {defaultMessage: "Approved"},
    rejected: {defaultMessage: "Rejected"},
    pending: {defaultMessage: "Pending"}
});

const Documents = () => {
    const intl = useIntl();

    const columns = useMemo(
        () => [
            {
                field: "name",
                minWidth: 150,
                flex: 1,
                headerName: intl.formatMessage(messages.name)
            },
            {
                field: "description",
                minWidth: 200,
                flex: 1,
                headerName: intl.formatMessage(messages.description)
            },
            {
                field: "pending_count",
                minWidth: 100,
                flex: 0.5,
                headerName: intl.formatMessage(messages.pending)
            },
            {
                field: "approved_count",
                minWidth: 100,
                flex: 0.5,
                headerName: intl.formatMessage(messages.approved)
            },
            {
                field: "rejected_count",
                minWidth: 100,
                flex: 0.5,
                headerName: intl.formatMessage(messages.rejected)
            },
            {
                field: "action",
                minWidth: 100,
                flex: 0.5,
                headerAlign: "right",
                headerName: intl.formatMessage(messages.action),
                align: "right",
                renderCell: ({row: document}) => {
                    return (
                        <Stack direction="row" spacing={1}>
                            <DocumentDelete document={document} />
                            <DocumentEdit document={document} />
                        </Stack>
                    );
                }
            }
        ],
        [intl]
    );

    const url = route("admin.required-document.paginate");

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

export default Documents;
