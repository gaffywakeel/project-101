import React, {Fragment, useCallback, useRef, useState} from "react";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {Link as RouterLink} from "react-router-dom";
import {notify} from "@/utils/index";
import {useAuth} from "@/models/Auth";
import {errorHandler, route, useFormRequest} from "@/services/Http";
import {
    Box,
    Checkbox,
    Container,
    InputAdornment,
    Link,
    Stack,
    Typography
} from "@mui/material";
import {has, upperFirst} from "lodash";
import Form, {ControlLabel, TextField} from "@/components/Form";
import ReCaptcha, {recaptchaSubmit} from "@/components/ReCaptcha";
import {AuthContent, SectionCard, StyledPage} from "../_components/styled";
import HintLayout from "../_components/HintLayout";
import illustrationLogin from "@/static/login-illustration.png";
import Divider from "@mui/material/Divider";
import {LoadingButton} from "@mui/lab";
import PersonIcon from "@mui/icons-material/Person";
import EmailIcon from "@mui/icons-material/Email";
import PasswordIcon from "@mui/icons-material/Password";
import LockIcon from "@mui/icons-material/Lock";
import Typewriter from "typewriter-effect";
import context from "@/contexts/AppContext";
import {useRecaptcha} from "@/hooks/settings";
import DemoLogin from "./_components/DemoLogin";

const messages = defineMessages({
    username: {defaultMessage: "Username"},
    email: {defaultMessage: "Email"},
    password: {defaultMessage: "Password"},
    tokenTitle: {defaultMessage: "Two Factor Verification"},
    token: {defaultMessage: "Token"},
    success: {defaultMessage: "Login was successful."},
    rememberMe: {defaultMessage: "Remember me"},
    lineOne: {defaultMessage: "Start your Crypto Portfolio today!"},
    lineTwo: {defaultMessage: "Buy & Sell Crypto with Credit Card"},
    lineThree: {defaultMessage: "Get paid instantly via Bank Transfer"},
    lineFive: {defaultMessage: "... and many more to come!"},
    title: {defaultMessage: "Login"}
});

const Login = () => {
    const auth = useAuth();
    const recaptchaRef = useRef();
    const intl = useIntl();
    const [form] = Form.useForm();
    const [withToken, setWithToken] = useState(false);
    const [request, loading] = useFormRequest(form);

    const recaptcha = useRecaptcha();

    const submitForm = useCallback(
        (values) => {
            request
                .post(route("auth.login"), values)
                .then(({data}) => {
                    notify.success(intl.formatMessage(messages.success));

                    if (data.intended) {
                        window.location.replace(data.intended);
                    } else {
                        window.location.reload();
                    }
                })
                .catch(
                    errorHandler((e) => {
                        if (has(e, "response.data.errors.token")) {
                            setWithToken(true);
                        }
                    })
                );
        },
        [request, intl]
    );

    const onSubmit = recaptchaSubmit(form, recaptchaRef);

    return (
        <StyledPage title={intl.formatMessage(messages.title)}>
            <HintLayout>
                <FormattedMessage defaultMessage="Don't have an account?" />

                <Link
                    underline="none"
                    component={RouterLink}
                    variant="subtitle2"
                    to="/auth/register"
                    sx={{ml: 1}}>
                    <FormattedMessage defaultMessage="Get started" />
                </Link>
            </HintLayout>

            <SectionCard sx={{display: {xs: "none", md: "block"}}}>
                <Stack sx={{p: 1, mb: 5}}>
                    <Typography variant="body2">
                        <FormattedMessage defaultMessage="Hi, welcome back." />
                    </Typography>

                    <Typography
                        sx={{minHeight: 100, fontWeight: 600}}
                        variant="h3">
                        <Typewriter
                            options={{
                                strings: [
                                    intl.formatMessage(messages.lineOne),
                                    intl.formatMessage(messages.lineTwo),
                                    intl.formatMessage(messages.lineThree),
                                    intl.formatMessage(messages.lineFive)
                                ],
                                autoStart: true,
                                skipAddStyles: true,
                                loop: true,
                                pauseFor: 3000
                            }}
                        />
                    </Typography>
                </Stack>

                <Stack justifyContent="center" sx={{height: 440}}>
                    <img src={illustrationLogin} alt="login" />
                </Stack>
            </SectionCard>

            <Container>
                <AuthContent>
                    <Stack direction="row" alignItems="center" sx={{mb: 5}}>
                        <Box sx={{flexGrow: 1}}>
                            <Typography variant="h4" gutterBottom>
                                <FormattedMessage
                                    defaultMessage="Sign in to {name}"
                                    values={{name: upperFirst(context.name)}}
                                />
                            </Typography>
                            <Typography sx={{color: "text.secondary"}}>
                                <FormattedMessage defaultMessage="Enter your details below." />
                            </Typography>
                        </Box>
                    </Stack>

                    <Form form={form} onFinish={submitForm}>
                        <Stack spacing={3}>
                            {auth.credential() === "name" ? (
                                <Form.Item
                                    name="name"
                                    rules={[{required: true}]}
                                    label={intl.formatMessage(
                                        messages.username
                                    )}>
                                    <TextField
                                        fullWidth
                                        InputProps={{
                                            startAdornment: (
                                                <InputAdornment position="start">
                                                    <PersonIcon />
                                                </InputAdornment>
                                            )
                                        }}
                                    />
                                </Form.Item>
                            ) : (
                                <Form.Item
                                    name="email"
                                    rules={[{required: true, type: "email"}]}
                                    label={intl.formatMessage(messages.email)}>
                                    <TextField
                                        fullWidth
                                        type="email"
                                        InputProps={{
                                            startAdornment: (
                                                <InputAdornment position="start">
                                                    <EmailIcon />
                                                </InputAdornment>
                                            )
                                        }}
                                    />
                                </Form.Item>
                            )}

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

                            {withToken && <TokenInput />}

                            <Stack
                                direction="row"
                                justifyContent="space-between"
                                alignItems="center"
                                sx={{my: 2}}>
                                <Form.Item
                                    name="remember"
                                    valuePropName="checked"
                                    initialValue={true}
                                    label={intl.formatMessage(
                                        messages.rememberMe
                                    )}>
                                    <ControlLabel>
                                        <Checkbox />
                                    </ControlLabel>
                                </Form.Item>

                                <Link
                                    component={RouterLink}
                                    to="/auth/forgot-password"
                                    variant="subtitle2">
                                    <FormattedMessage defaultMessage="Forgot password?" />
                                </Link>
                            </Stack>

                            {recaptcha.enable && (
                                <Form.Item
                                    rules={[{required: true}]}
                                    name="recaptcha">
                                    <ReCaptcha ref={recaptchaRef} />
                                </Form.Item>
                            )}

                            <Stack direction="row" spacing={2}>
                                <LoadingButton
                                    fullWidth
                                    variant="contained"
                                    size="large"
                                    onClick={onSubmit}
                                    loading={loading}>
                                    <FormattedMessage defaultMessage="Login" />
                                </LoadingButton>

                                <DemoLogin />
                            </Stack>
                        </Stack>
                    </Form>

                    <Typography
                        variant="body2"
                        sx={{display: {xs: "block", sm: "none"}, mt: 3}}
                        align="center">
                        <FormattedMessage defaultMessage="Don't have an account?" />

                        <Link
                            component={RouterLink}
                            to="/auth/register"
                            sx={{ml: 1}}>
                            <FormattedMessage defaultMessage="Get started" />
                        </Link>
                    </Typography>
                </AuthContent>
            </Container>
        </StyledPage>
    );
};

const TokenInput = () => {
    const intl = useIntl();

    return (
        <Fragment>
            <Divider>
                <FormattedMessage defaultMessage="Two Factor Verification" />
            </Divider>

            <Form.Item
                name="token"
                rules={[{required: true}]}
                label={intl.formatMessage(messages.token)}>
                <TextField
                    fullWidth
                    InputProps={{
                        startAdornment: (
                            <InputAdornment position="start">
                                <LockIcon />
                            </InputAdornment>
                        )
                    }}
                />
            </Form.Item>
        </Fragment>
    );
};

export {Login as Component};
export default Login;
