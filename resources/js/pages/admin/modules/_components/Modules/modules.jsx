import React, {useMemo} from "react";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {isEmpty} from "lodash";
import UserCell from "@/components/TableCells/UserCell";
import Label from "@/components/Label";
import {Card, Stack} from "@mui/material";
import ModuleOperator from "./ModuleOperator";
import ModuleSwitch from "./ModuleSwitch";
import {route} from "@/services/Http";
import TrapScrollBox from "@/components/TrapScrollBox";
import AsyncTable from "@/components/AsyncTable";
import ActionBar from "./ActionBar";

const messages = defineMessages({
    title: {defaultMessage: "Modules"},
    name: {defaultMessage: "Name"},
    module: {defaultMessage: "Module"},
    operator: {defaultMessage: "Operator"},
    status: {defaultMessage: "Status"},
    action: {defaultMessage: "Action"}
});

const Modules = () => {
    const intl = useIntl();

    const columns = useMemo(
        () => [
            {
                field: "title",
                minWidth: 150,
                flex: 1,
                headerName: intl.formatMessage(messages.module)
            },
            {
                field: "operator",
                minWidth: 200,
                flex: 1,
                headerName: intl.formatMessage(messages.operator),
                renderCell: ({value: operator}) => {
                    return isEmpty(operator) ? (
                        <FormattedMessage defaultMessage="Unavailable" />
                    ) : (
                        <UserCell user={operator} />
                    );
                }
            },
            {
                field: "status",
                minWidth: 100,
                flex: 0.5,
                headerName: intl.formatMessage(messages.status),
                renderCell: ({value: status}) => {
                    return status ? (
                        <Label variant="ghost" color="success">
                            <FormattedMessage defaultMessage="Enabled" />
                        </Label>
                    ) : (
                        <Label variant="ghost" color="error">
                            <FormattedMessage defaultMessage="Disabled" />
                        </Label>
                    );
                }
            },
            {
                field: "action",
                width: 150,
                headerName: intl.formatMessage(messages.action),
                align: "right",
                renderCell: ({row: module}) => {
                    return (
                        <Stack direction="row" spacing={1}>
                            <ModuleOperator module={module} />
                            <ModuleSwitch module={module} />
                        </Stack>
                    );
                }
            }
        ],
        [intl]
    );

    const url = route("admin.module.paginate");

    return (
        <Card>
            <TrapScrollBox>
                <AsyncTable
                    columns={columns}
                    components={{Toolbar: ActionBar}}
                    getRowId={(row) => row.name}
                    url={url}
                />
            </TrapScrollBox>
        </Card>
    );
};

export default Modules;
