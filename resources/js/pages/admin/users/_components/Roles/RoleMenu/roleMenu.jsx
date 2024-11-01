import React, {Fragment, useCallback, useContext, useMemo} from "react";
import {IconButton, MenuItem} from "@mui/material";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import NorthIcon from "@mui/icons-material/North";
import SouthIcon from "@mui/icons-material/South";
import {errorHandler, route, useFormRequest, useRequest} from "@/services/Http";
import TableContext from "@/contexts/TableContext";
import {isEmpty} from "lodash";
import LoadingIcon from "@/components/LoadingIcon";
import MoreVertIcon from "@mui/icons-material/MoreVert";
import Dropdown from "@/components/Dropdown";
import EditIcon from "@mui/icons-material/Edit";
import useModal from "@/hooks/useModal";
import Form from "@/components/Form";
import {notify} from "@/utils/index";
import RoleForm from "../RoleForm";

const messages = defineMessages({
    success: {defaultMessage: "Role was updated."},
    update: {defaultMessage: "Update Role"}
});

const RoleMenu = ({role}) => {
    const intl = useIntl();
    const [modal, modalElements] = useModal();
    const [request, loading] = useRequest();
    const {reload: reloadTable} = useContext(TableContext);

    const moveUp = useCallback(() => {
        request
            .post(route("admin.roles.move-up", {role: role.id}))
            .then(() => reloadTable())
            .catch(errorHandler());
    }, [request, reloadTable, role]);

    const moveDown = useCallback(() => {
        request
            .post(route("admin.roles.move-down", {role: role.id}))
            .then(() => reloadTable())
            .catch(errorHandler());
    }, [request, reloadTable, role]);

    const update = useCallback(() => {
        modal.confirm({
            title: intl.formatMessage(messages.update),
            content: <UpdateForm role={role} />,
            dialogProps: {fullWidth: true}
        });
    }, [modal, intl, role]);

    const menuItems = useMemo(() => {
        const items = [];

        if (role.move_up_policy) {
            items.push(
                <MenuItem key={0} onClick={moveUp}>
                    <NorthIcon sx={{mr: 2}} />
                    <FormattedMessage defaultMessage="Move Up" />
                </MenuItem>
            );
        }

        if (role.move_down_policy) {
            items.push(
                <MenuItem key={1} onClick={moveDown}>
                    <SouthIcon sx={{mr: 2}} />
                    <FormattedMessage defaultMessage="Move Down" />
                </MenuItem>
            );
        }

        if (role.update_policy) {
            items.push(
                <MenuItem key={2} onClick={update}>
                    <EditIcon sx={{mr: 2}} />
                    <FormattedMessage defaultMessage="Update" />
                </MenuItem>
            );
        }

        return items;
    }, [role, moveUp, moveDown, update]);

    if (isEmpty(menuItems)) {
        return null;
    }

    return (
        <Fragment>
            <Dropdown menuItems={menuItems} component={IconButton}>
                <LoadingIcon component={MoreVertIcon} loading={loading} />
            </Dropdown>

            {modalElements}
        </Fragment>
    );
};

const UpdateForm = ({closeModal, role}) => {
    const [form] = Form.useForm();
    const intl = useIntl();
    const [formRequest, formLoading] = useFormRequest(form);
    const {reload: reloadTable} = useContext(TableContext);

    const submitForm = useCallback(
        (values) => {
            const url = route("admin.roles.update", {
                role: role.id
            });

            formRequest
                .put(url, values)
                .then(() => {
                    notify.success(intl.formatMessage(messages.success));
                    closeModal();
                    reloadTable();
                })
                .catch(errorHandler());
        },
        [closeModal, formRequest, intl, role, reloadTable]
    );

    return (
        <RoleForm
            form={form}
            formLoading={formLoading}
            submitForm={submitForm}
            role={role}
        />
    );
};

export default RoleMenu;
