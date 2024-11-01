import React, {useCallback, useContext} from "react";
import {Button} from "@mui/material";
import {defineMessages, useIntl} from "react-intl";
import AddIcon from "@mui/icons-material/Add";
import useModal from "@/hooks/useModal";
import Form from "@/components/Form";
import {errorHandler, route, useFormRequest} from "@/services/Http";
import {notify} from "@/utils/index";
import SearchTable from "@/components/SearchTable";
import TableContext from "@/contexts/TableContext";
import ActionToolbar from "@/components/ActionToolbar";
import Authorized from "@/components/Authorized";
import RoleForm from "../RoleForm";

const messages = defineMessages({
    success: {defaultMessage: "Role was created"},
    search: {defaultMessage: "Search by name..."},
    createRole: {defaultMessage: "Create Role"}
});

const ActionBar = () => {
    const intl = useIntl();
    const [modal, modalElements] = useModal();

    const createRole = useCallback(() => {
        modal.confirm({
            title: intl.formatMessage(messages.createRole),
            content: <CreateForm />,
            dialogProps: {fullWidth: true}
        });
    }, [modal, intl]);

    return (
        <ActionToolbar>
            <SearchTable field="name" />

            <Authorized permission="create:roles">
                {modalElements}

                <Button
                    size="small"
                    variant="contained"
                    onClick={createRole}
                    disableElevation>
                    <AddIcon />
                </Button>
            </Authorized>
        </ActionToolbar>
    );
};

const CreateForm = ({closeModal}) => {
    const [form] = Form.useForm();
    const intl = useIntl();
    const [formRequest, formLoading] = useFormRequest(form);
    const {reload: reloadTable} = useContext(TableContext);

    const submitForm = useCallback(
        (values) => {
            formRequest
                .post(route("admin.roles.store"), values)
                .then(() => {
                    notify.success(intl.formatMessage(messages.success));
                    closeModal();
                    reloadTable();
                })
                .catch(errorHandler());
        },
        [closeModal, formRequest, intl, reloadTable]
    );

    return (
        <RoleForm
            form={form}
            formLoading={formLoading}
            submitForm={submitForm}
        />
    );
};

export default ActionBar;
