import React, {useMemo} from "react";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {route} from "@/services/Http";
import {Card, Chip, Stack} from "@mui/material";
import ActionBar from "./ActionBar";
import RoleDelete from "./RoleDelete";
import TrapScrollBox from "@/components/TrapScrollBox";
import AsyncTable from "@/components/AsyncTable";
import PermissionsCell from "@/components/TableCells/PermissionsCell";
import RoleMenu from "./RoleMenu";
import Label from "@/components/Label";
import DateCell from "@/components/TableCells/DateCell";

const messages = defineMessages({
    name: {defaultMessage: "Name"},
    permissions: {defaultMessage: "Permissions"},
    order: {defaultMessage: "Order"},
    action: {defaultMessage: "Action"},
    users: {defaultMessage: "Users"},
    created: {defaultMessage: "Created"},
    type: {defaultMessage: "Type"}
});

const Roles = () => {
    const intl = useIntl();

    const columns = useMemo(
        () => [
            {
                field: "name",
                minWidth: 100,
                flex: 1,
                headerName: intl.formatMessage(messages.name)
            },
            {
                field: "permissions",
                minWidth: 200,
                flex: 1,
                headerName: intl.formatMessage(messages.permissions),
                renderCell: ({row: role}) => (
                    <PermissionsCell
                        permissions={role.permissions}
                        isAdministrator={role.is_administrator}
                    />
                )
            },
            {
                field: "is_protected",
                minWidth: 100,
                flex: 0.8,
                headerName: intl.formatMessage(messages.type),
                renderCell: ({value}) => {
                    return value ? (
                        <Label variant="ghost" color="default">
                            <FormattedMessage defaultMessage="Default" />
                        </Label>
                    ) : (
                        <Label variant="ghost" color="primary">
                            <FormattedMessage defaultMessage="Custom" />
                        </Label>
                    );
                }
            },
            {
                field: "users_count",
                minWidth: 100,
                flex: 0.5,
                headerName: intl.formatMessage(messages.users),
                renderCell: ({value: label}) => {
                    return <Chip size="small" label={label} />;
                }
            },
            {
                field: "created_at",
                minWidth: 100,
                flex: 0.5,
                type: "dateTime",
                filterable: true,
                headerName: intl.formatMessage(messages.created),
                renderCell: ({value}) => <DateCell value={value} />
            },
            {
                field: "action",
                minWidth: 100,
                flex: 0.5,
                headerAlign: "right",
                headerName: intl.formatMessage(messages.action),
                align: "right",
                renderCell: ({row: role}) => {
                    return (
                        <Stack direction="row" spacing={1}>
                            <RoleDelete role={role} />
                            <RoleMenu role={role} />
                        </Stack>
                    );
                }
            }
        ],
        [intl]
    );

    const tableUrl = route("admin.roles.paginate");

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

export default Roles;
