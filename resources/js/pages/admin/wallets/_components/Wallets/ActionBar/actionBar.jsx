import React, {useCallback, useContext, useEffect, useState} from "react";
import {Button, Grid} from "@mui/material";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import AddIcon from "@mui/icons-material/Add";
import useModal from "@/hooks/useModal";
import Form, {AutoComplete, TextField} from "@/components/Form";
import {errorHandler, route, useFormRequest, useRequest} from "@/services/Http";
import {notify} from "@/utils/index";
import {LoadingButton} from "@mui/lab";
import SearchTable from "@/components/SearchTable";
import TableContext from "@/contexts/TableContext";
import ModalActions from "@/components/ModalActions";
import ActionToolbar from "@/components/ActionToolbar";
import Authorized from "@/components/Authorized";

const messages = defineMessages({
    success: {defaultMessage: "Wallet was added."},
    search: {defaultMessage: "Search coin..."},
    addWallet: {defaultMessage: "Add Wallet"},
    confirmations: {defaultMessage: "Confirmations"},
    adapter: {defaultMessage: "Adapter"}
});

const ActionBar = () => {
    const intl = useIntl();
    const [modal, modalElements] = useModal();

    const addWallet = useCallback(() => {
        modal.confirm({
            title: intl.formatMessage(messages.addWallet),
            content: <CreateForm />,
            dialogProps: {fullWidth: true}
        });
    }, [modal, intl]);

    return (
        <ActionToolbar>
            <SearchTable
                field="searchCoin"
                placeholder={intl.formatMessage(messages.search)}
                withParams={true}
            />

            <Authorized permission="create:wallets">
                {modalElements}

                <Button
                    size="small"
                    variant="contained"
                    onClick={addWallet}
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
    const [adapters, setAdapters] = useState([]);
    const [formRequest, formLoading] = useFormRequest(form);
    const {reload: reloadTable} = useContext(TableContext);
    const [request, loading] = useRequest();

    const fetchAdapters = useCallback(() => {
        request
            .get(route("coin-adapters.index"))
            .then(({data}) => setAdapters(data))
            .catch(errorHandler());
    }, [request]);

    useEffect(() => {
        fetchAdapters();
    }, [fetchAdapters]);

    const submitForm = useCallback(
        (values) => {
            formRequest
                .post(route("wallets.store"), normalize(values))
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
        <Form form={form} onFinish={submitForm}>
            <Grid container spacing={1}>
                <Grid item xs={8}>
                    <Form.Item
                        name="adapter"
                        label={intl.formatMessage(messages.adapter)}
                        rules={[{required: true}]}>
                        <AutoComplete
                            options={adapters}
                            getOptionLabel={(option) => option.name}
                            isOptionEqualToValue={(option, value) =>
                                option.identifier === value.identifier
                            }
                            loading={loading}
                            renderOption={(props, option) => (
                                <li {...props} key={option.identifier}>
                                    {option.name}
                                </li>
                            )}
                        />
                    </Form.Item>
                </Grid>

                <Grid item xs={4}>
                    <Form.Item
                        name="min_conf"
                        normalize={(v) => v && parseInt(v)}
                        label={intl.formatMessage(messages.confirmations)}
                        rules={[{required: true, type: "number"}]}
                        initialValue={3}>
                        <TextField fullWidth />
                    </Form.Item>
                </Grid>
            </Grid>

            <ModalActions>
                <LoadingButton
                    variant="contained"
                    loading={formLoading}
                    disabled={loading}
                    type="submit">
                    <FormattedMessage defaultMessage="Submit" />
                </LoadingButton>
            </ModalActions>
        </Form>
    );
};

const normalize = (values) => ({
    identifier: values.adapter?.identifier,
    min_conf: values.min_conf
});

export default ActionBar;
