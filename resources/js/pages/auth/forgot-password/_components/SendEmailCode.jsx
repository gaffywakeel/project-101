import React, {useCallback, useRef} from "react";
import ReCaptcha, {recaptchaSubmit} from "@/components/ReCaptcha";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {useRecaptcha} from "@/hooks/settings";
import {Button, InputAdornment, Stack, Typography} from "@mui/material";
import Form, {TextField} from "@/components/Form";
import EmailIcon from "@mui/icons-material/Email";
import {LoadingButton} from "@mui/lab";
import {Link as RouterLink} from "react-router-dom";
import {notify} from "@/utils/index";
import {errorHandler, route, useFormRequest} from "@/services/Http";

const messages = defineMessages({
    email: {defaultMessage: "Email"},
    codeSent: {defaultMessage: "Verification code was sent."}
});

const SendEmailCode = ({email, onSuccess}) => {
    const intl = useIntl();
    const recaptcha = useRecaptcha();
    const recaptchaRef = useRef();
    const [form] = Form.useForm();
    const [formRequest, formLoading] = useFormRequest(form);
    const onSubmit = recaptchaSubmit(form, recaptchaRef);

    const submitForm = useCallback(
        (values) => {
            formRequest
                .post(route("auth.reset-password.send-email-code"), values)
                .then(() => {
                    notify.success(intl.formatMessage(messages.codeSent));
                    onSuccess(values.email);
                })
                .catch(errorHandler());
        },
        [intl, formRequest, onSuccess]
    );

    return (
        <Form form={form} onFinish={submitForm} initialValues={{email}}>
            <Typography variant="h3" paragraph>
                <FormattedMessage defaultMessage="Forgot your password?" />
            </Typography>

            <Typography sx={{color: "text.secondary", mb: 5}}>
                <FormattedMessage defaultMessage="Please enter the email address associated with your account to get a verification code." />
            </Typography>

            <Stack spacing={3}>
                <Form.Item
                    name="email"
                    label={intl.formatMessage(messages.email)}
                    rules={[{required: true, type: "email"}]}>
                    <TextField
                        type="email"
                        id="recovery-email"
                        fullWidth
                        InputProps={{
                            startAdornment: (
                                <InputAdornment position="start">
                                    <EmailIcon />
                                </InputAdornment>
                            )
                        }}
                    />
                </Form.Item>

                {recaptcha.enable && (
                    <Form.Item rules={[{required: true}]} name="recaptcha">
                        <ReCaptcha ref={recaptchaRef} />
                    </Form.Item>
                )}

                <LoadingButton
                    fullWidth
                    variant="contained"
                    loading={formLoading}
                    size="large"
                    onClick={onSubmit}>
                    <FormattedMessage defaultMessage="Send Code" />
                </LoadingButton>
            </Stack>

            <Button
                component={RouterLink}
                to="/auth/login"
                size="large"
                fullWidth={true}
                sx={{mt: 1}}>
                <FormattedMessage defaultMessage="Go Back" />
            </Button>
        </Form>
    );
};

export default SendEmailCode;
