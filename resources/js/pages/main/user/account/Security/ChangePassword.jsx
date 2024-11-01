import React, {useCallback} from "react";
import {
    Card,
    CardActions,
    CardContent,
    CardHeader,
    Divider,
    InputAdornment,
    Stack
} from "@mui/material";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import Form, {TextField} from "@/components/Form";
import PasswordIcon from "@mui/icons-material/Password";
import {LoadingButton} from "@mui/lab";
import {errorHandler, route, useFormRequest} from "@/services/Http";
import {notify} from "@/utils/index";
import {passwordConfirmation} from "@/utils/form";

const messages = defineMessages({
    oldPassword: {defaultMessage: "Old Password"},
    password: {defaultMessage: "Password"},
    passwordReset: {defaultMessage: "Password was reset."},
    confirmPassword: {defaultMessage: "Confirm Password"}
});

const ChangePassword = () => {
    const [form] = Form.useForm();
    const intl = useIntl();
    const [formRequest, formLoading] = useFormRequest(form);

    const submitForm = useCallback(
        (values) => {
            formRequest
                .post(route("user.change-password"), values)
                .then(() => {
                    notify.success(intl.formatMessage(messages.passwordReset));
                    window.location.reload();
                })
                .catch(errorHandler());
        },
        [formRequest, intl]
    );

    return (
        <Form form={form} onFinish={submitForm}>
            <Card>
                <CardHeader
                    title={
                        <FormattedMessage defaultMessage="Change Password" />
                    }
                />

                <CardContent>
                    <Stack spacing={2}>
                        <Form.Item
                            name="old_password"
                            label={intl.formatMessage(messages.oldPassword)}
                            rules={[{required: true}]}>
                            <TextField
                                fullWidth
                                type="password"
                                InputProps={{
                                    startAdornment: (
                                        <InputAdornment position="start">
                                            <PasswordIcon />
                                        </InputAdornment>
                                    )
                                }}
                            />
                        </Form.Item>

                        <Divider sx={{my: 2}}>
                            <FormattedMessage defaultMessage="New Password" />
                        </Divider>

                        <Form.Item
                            name="password"
                            label={intl.formatMessage(messages.password)}
                            rules={[{required: true}]}>
                            <TextField
                                fullWidth
                                type="password"
                                InputProps={{
                                    startAdornment: (
                                        <InputAdornment position="start">
                                            <PasswordIcon />
                                        </InputAdornment>
                                    )
                                }}
                            />
                        </Form.Item>

                        <Form.Item
                            name="password_confirmation"
                            label={intl.formatMessage(messages.confirmPassword)}
                            dependencies={["password"]}
                            rules={[
                                passwordConfirmation(intl, "password"),
                                {required: true}
                            ]}>
                            <TextField
                                fullWidth
                                type="password"
                                InputProps={{
                                    startAdornment: (
                                        <InputAdornment position="start">
                                            <PasswordIcon />
                                        </InputAdornment>
                                    )
                                }}
                            />
                        </Form.Item>
                    </Stack>
                </CardContent>

                <CardActions sx={{justifyContent: "flex-end"}}>
                    <LoadingButton
                        variant="contained"
                        type="submit"
                        loading={formLoading}>
                        <FormattedMessage defaultMessage="Submit" />
                    </LoadingButton>
                </CardActions>
            </Card>
        </Form>
    );
};

export default ChangePassword;
