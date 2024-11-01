import React, {useMemo} from "react";
import ModalContent from "@/components/ModalContent";
import Form, {Checkbox, Hidden, TextField} from "@/components/Form";
import {Grid, Paper, Stack, Typography} from "@mui/material";
import ModalActions from "@/components/ModalActions";
import {LoadingButton} from "@mui/lab";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {groupBy, isEmpty, upperCase} from "lodash";
import {useAuth} from "@/models/Auth";
import Divider from "@mui/material/Divider";

const messages = defineMessages({
    name: {defaultMessage: "Name"}
});

const RoleForm = ({form, submitForm, formLoading, role}) => {
    const auth = useAuth();
    const intl = useIntl();

    const groups = useMemo(() => {
        const resources = groupBy(auth.permissions(), "group");
        return Object.entries(resources);
    }, [auth]);

    return (
        <Form form={form} onFinish={submitForm}>
            <ModalContent spacing={3}>
                {(isEmpty(role) || role.update_name_policy) && (
                    <Form.Item
                        name="name"
                        initialValue={role?.name}
                        label={intl.formatMessage(messages.name)}
                        rules={[{required: true}]}>
                        <TextField fullWidth />
                    </Form.Item>
                )}

                <Stack spacing={2}>
                    {groups.map(([group, permissions], index, entries) => (
                        <PermissionGroup
                            key={group}
                            permissions={permissions}
                            group={group}
                            offset={calculateOffset(entries, index)}
                            role={role}
                        />
                    ))}
                </Stack>
            </ModalContent>

            <ModalActions>
                <LoadingButton
                    variant="contained"
                    loading={formLoading}
                    type="submit">
                    <FormattedMessage defaultMessage="Submit" />
                </LoadingButton>
            </ModalActions>
        </Form>
    );
};

const PermissionGroup = ({group, offset, permissions, role}) => {
    const hasPermission = (name) => {
        if (isEmpty(role)) {
            return false;
        }

        const index = role.permissions.findIndex((o) => {
            return o.name === name;
        });

        return index >= 0;
    };

    return (
        <Paper variant="outlined" elevation={0}>
            <Typography
                variant="overline"
                color="text.secondary"
                sx={{px: 2, py: 1}}
                display="block">
                {upperCase(group)}
            </Typography>

            <Divider sx={{borderStyle: "dashed"}} />

            <Grid container spacing={0.5} sx={{px: 2, py: 1}}>
                {permissions.map((permission, index) => (
                    <Grid item xs={12} sm={6} key={permission.name}>
                        <Form.Item
                            name={["permissions", offset + index, "name"]}
                            initialValue={permission.name}>
                            <Hidden />
                        </Form.Item>

                        <Form.Item
                            name={["permissions", offset + index, "value"]}
                            initialValue={hasPermission(permission.name)}
                            valuePropName="checked"
                            label={permission.title}>
                            <Checkbox />
                        </Form.Item>
                    </Grid>
                ))}
            </Grid>
        </Paper>
    );
};

const calculateOffset = (entries, index) => {
    let offset = 0;

    for (let i = 0; i < index; i++) {
        offset = offset + entries[i][1].length;
    }

    return offset;
};

export default RoleForm;
