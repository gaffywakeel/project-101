import React, {useCallback, useEffect, useMemo, useRef} from "react";
import Form, {RichEditor, TextField, UploadPhoto} from "@/components/Form";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import PhoneInput, {phoneValidator} from "@/components/PhoneInput";
import {LoadingButton} from "@mui/lab";
import {useCommerceAccount} from "@/hooks/accounts";
import {useDispatch} from "react-redux";
import {fetchCommerceAccount} from "@/redux/slices/commerce";
import {errorHandler, route, useFormRequest} from "@/services/Http";
import {notify} from "@/utils/index";
import {
    Card,
    CardContent,
    CardHeader,
    Grid,
    Stack,
    Typography
} from "@mui/material";

const messages = defineMessages({
    success: {defaultMessage: "Account was updated."},
    name: {defaultMessage: "Business Name"},
    namePlace: {defaultMessage: "eCommerce Store"},
    about: {defaultMessage: "About"},
    email: {defaultMessage: "Email"},
    emailPlace: {defaultMessage: "info@example.com"},
    site: {defaultMessage: "Website"},
    sitePlace: {defaultMessage: "https://example.com"},
    logo: {defaultMessage: "Logo"},
    phone: {defaultMessage: "Phone"}
});

const AccountForm = () => {
    const intl = useIntl();
    const dispatch = useDispatch();
    const [form] = Form.useForm();
    const [formRequest, formLoading] = useFormRequest(form);
    const {account} = useCommerceAccount();

    useEffect(() => {
        if (account.isNotEmpty()) {
            form.resetFields();
        }
    }, [form, account]);

    const submitForm = useCallback(
        (values) => {
            let response;

            if (account.isEmpty()) {
                response = formRequest.postForm(
                    route("commerce-account.create"),
                    {...values, _method: "post"}
                );
            } else {
                response = formRequest.postForm(
                    route("commerce-account.update"),
                    {...values, _method: "patch"}
                );
            }

            response
                .then(() => {
                    notify.success(intl.formatMessage(messages.success));
                    dispatch(fetchCommerceAccount());
                })
                .catch(errorHandler());
        },
        [intl, account, formRequest, dispatch]
    );

    const initialValues = useMemo(
        () => ({
            name: account.name,
            website: account.website,
            about: account.about,
            phone: account.phone,
            email: account.email
        }),
        [account]
    );

    return (
        <Form form={form} initialValues={initialValues} onFinish={submitForm}>
            <Grid container spacing={3}>
                <Grid item xs={12} md={8}>
                    <Basic />
                </Grid>

                <Grid item xs={12} md={4}>
                    <Stack spacing={2}>
                        <Extra />

                        <LoadingButton
                            type="submit"
                            variant="contained"
                            size="large"
                            loading={formLoading}
                            fullWidth>
                            {account.isEmpty() ? (
                                <FormattedMessage defaultMessage="Create Account" />
                            ) : (
                                <FormattedMessage defaultMessage="Update Account" />
                            )}
                        </LoadingButton>
                    </Stack>
                </Grid>
            </Grid>
        </Form>
    );
};

const Basic = () => {
    const itlRef = useRef();
    const intl = useIntl();

    return (
        <Card>
            <CardHeader
                title={<FormattedMessage defaultMessage="Store Details" />}
            />

            <CardContent>
                <Stack spacing={3}>
                    <Form.Item
                        name="name"
                        label={intl.formatMessage(messages.name)}
                        rules={[{required: true}]}>
                        <TextField fullWidth />
                    </Form.Item>

                    <Stack spacing={1}>
                        <Typography variant="subtitle2" color="text.secondary">
                            <FormattedMessage defaultMessage="About" />
                        </Typography>

                        <Form.Item
                            name="about"
                            label={intl.formatMessage(messages.about)}
                            rules={[{required: true}]}>
                            <RichEditor />
                        </Form.Item>
                    </Stack>

                    <Stack
                        spacing={{xs: 3, sm: 2}}
                        direction={{xs: "column", sm: "row"}}>
                        <Form.Item
                            name="email"
                            label={intl.formatMessage(messages.email)}
                            rules={[{type: "email", required: true}]}>
                            <TextField fullWidth />
                        </Form.Item>

                        <Form.Item
                            name="phone"
                            label={intl.formatMessage(messages.phone)}
                            rules={[phoneValidator(itlRef, intl)]}>
                            <PhoneInput itlRef={itlRef} fullWidth />
                        </Form.Item>
                    </Stack>

                    <Form.Item
                        name="website"
                        label={intl.formatMessage(messages.site)}
                        rules={[{type: "url"}]}>
                        <TextField fullWidth />
                    </Form.Item>
                </Stack>
            </CardContent>
        </Card>
    );
};

const Extra = () => {
    const intl = useIntl();
    const {account} = useCommerceAccount();

    return (
        <Card>
            <CardHeader
                title={<FormattedMessage defaultMessage="Store Extras" />}
            />

            <CardContent>
                <Form.Item
                    name="logo"
                    label={intl.formatMessage(messages.logo)}>
                    <UploadPhoto preview={account.logo} rounded />
                </Form.Item>
            </CardContent>
        </Card>
    );
};

export default AccountForm;
