import React, {useCallback, useMemo, useRef} from "react";
import Form, {DatePicker, TextField} from "@/components/Form";
import {
    Card,
    CardActions,
    CardContent,
    IconButton,
    InputAdornment,
    MenuItem,
    Stack,
    Typography
} from "@mui/material";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import PhoneInput, {phoneValidator} from "@/components/PhoneInput";
import {LoadingButton} from "@mui/lab";
import useModal from "@/hooks/useModal";
import {useAuth} from "@/models/Auth";
import {useDispatch} from "react-redux";
import {errorHandler, route, useFormRequest} from "@/services/Http";
import {defaultTo} from "lodash";
import {normalizeDates} from "@/utils/form";
import {notify, parseDate} from "@/utils/index";
import {fetchUser, fetchVerification} from "@/redux/slices/auth";
import GppGoodIcon from "@mui/icons-material/GppGood";
import GppMaybeIcon from "@mui/icons-material/GppMaybe";
import EmailVerification from "./EmailVerification";
import PhoneVerification from "./PhoneVerification";
import {useCountries} from "@/hooks/global";

const messages = defineMessages({
    lastName: {defaultMessage: "Last Name"},
    firstName: {defaultMessage: "First Name"},
    dob: {defaultMessage: "Date of Birth"},
    country: {defaultMessage: "Country"},
    bio: {defaultMessage: "Bio"},
    phone: {defaultMessage: "Phone"},
    email: {defaultMessage: "Email"},
    profileUpdated: {defaultMessage: "Your profile was updated."},
    close: {defaultMessage: "Close"},
    verify: {defaultMessage: "Verify"}
});

const Profile = () => {
    const [form] = Form.useForm();
    const auth = useAuth();
    const dispatch = useDispatch();
    const [request, loading] = useFormRequest(form);
    const intl = useIntl();

    const submitForm = useCallback(
        (values) => {
            normalizeDates(values, "dob");

            request
                .patch(route("user.update"), values)
                .then(() => {
                    notify.success(intl.formatMessage(messages.profileUpdated));
                    dispatch(fetchUser());
                    dispatch(fetchVerification());
                })
                .catch(errorHandler());
        },
        [intl, request, dispatch]
    );

    const initialValues = useMemo(() => {
        return {
            last_name: auth.user.profile?.last_name,
            first_name: auth.user.profile?.first_name,
            dob: parseDate(auth.user.profile?.dob),
            bio: auth.user.profile?.bio,
            country: defaultTo(auth.user.country, ""),
            phone: defaultTo(auth.user.phone, ""),
            email: defaultTo(auth.user.email, "")
        };
    }, [auth]);

    return (
        <Form form={form} initialValues={initialValues} onFinish={submitForm}>
            <Card>
                <CardContent>
                    <Stack spacing={3}>
                        <Details initialValues={initialValues} />
                        <Contact initialValues={initialValues} />
                    </Stack>
                </CardContent>

                <CardActions sx={{justifyContent: "flex-end"}}>
                    <LoadingButton
                        loading={loading}
                        variant="contained"
                        type="submit">
                        <FormattedMessage defaultMessage="Save Changes" />
                    </LoadingButton>
                </CardActions>
            </Card>
        </Form>
    );
};

const Details = ({initialValues}) => {
    const intl = useIntl();
    const {countries} = useCountries();

    return (
        <Stack spacing={2}>
            <Typography variant="overline" color="text.secondary">
                <FormattedMessage defaultMessage="Basic Information" />
            </Typography>

            <Stack
                spacing={{xs: 3, sm: 2}}
                direction={{xs: "column", sm: "row"}}>
                <Form.Item
                    name="first_name"
                    label={intl.formatMessage(messages.firstName)}
                    rules={[{required: true}]}>
                    <TextField
                        disabled={Boolean(initialValues["first_name"])}
                        fullWidth
                    />
                </Form.Item>

                <Form.Item
                    name="last_name"
                    label={intl.formatMessage(messages.lastName)}
                    rules={[{required: true}]}>
                    <TextField
                        disabled={Boolean(initialValues["last_name"])}
                        fullWidth
                    />
                </Form.Item>
            </Stack>

            <Stack
                spacing={{xs: 3, sm: 2}}
                direction={{xs: "column", sm: "row"}}>
                <Form.Item
                    name="dob"
                    label={intl.formatMessage(messages.dob)}
                    rules={[{required: true}]}>
                    <DatePicker fullWidth />
                </Form.Item>

                <Form.Item
                    name="country"
                    rules={[{required: true}]}
                    label={intl.formatMessage(messages.country)}>
                    <TextField
                        disabled={Boolean(initialValues["country"])}
                        select
                        fullWidth>
                        {countries.map((country) => (
                            <MenuItem value={country.code} key={country.code}>
                                {`${country.name}`}
                            </MenuItem>
                        ))}
                    </TextField>
                </Form.Item>
            </Stack>

            <Form.Item
                name="bio"
                label={intl.formatMessage(messages.bio)}
                rules={[{required: false}]}>
                <TextField rows={3} multiline fullWidth />
            </Form.Item>
        </Stack>
    );
};

const Contact = ({initialValues}) => {
    const auth = useAuth();
    const intl = useIntl();
    const [modal, modalElements] = useModal();
    const itlRef = useRef();

    const showEmailVerification = useCallback(() => {
        modal.confirm({
            content: <EmailVerification email={auth.user.email} />
        });
    }, [modal, auth]);

    const showPhoneVerification = useCallback(() => {
        modal.confirm({
            content: <PhoneVerification phone={auth.user.phone} />
        });
    }, [modal, auth]);

    const appendVerification = useCallback(
        (params) => {
            const saved = initialValues[params.field];
            const props = {value: params.value};

            if (saved && params.value === saved) {
                props.InputProps = {
                    endAdornment: (
                        <InputAdornment position="end">
                            {params.status ? (
                                <GppGoodIcon color="success" />
                            ) : (
                                <IconButton
                                    onClick={params?.onClick}
                                    color="warning">
                                    <GppMaybeIcon />
                                </IconButton>
                            )}
                        </InputAdornment>
                    )
                };
            }
            return props;
        },
        [initialValues]
    );

    return (
        <Stack spacing={2}>
            <Typography variant="overline" color="text.secondary">
                <FormattedMessage defaultMessage="Contact" />
            </Typography>

            <Stack
                spacing={{xs: 3, sm: 2}}
                direction={{xs: "column", sm: "row"}}>
                <Form.Item
                    name="email"
                    label={intl.formatMessage(messages.email)}
                    rules={[{type: "email", required: true}]}
                    getValueProps={(itemValue) => {
                        return appendVerification({
                            field: "email",
                            status: auth.user.hasVerifiedEmail(),
                            onClick: showEmailVerification,
                            value: itemValue
                        });
                    }}>
                    <TextField fullWidth />
                </Form.Item>

                <Form.Item
                    name="phone"
                    label={intl.formatMessage(messages.phone)}
                    rules={[phoneValidator(itlRef, intl), {required: true}]}
                    getValueProps={(itemValue) => {
                        return appendVerification({
                            field: "phone",
                            status: auth.user.hasVerifiedPhone(),
                            onClick: showPhoneVerification,
                            value: itemValue
                        });
                    }}>
                    <PhoneInput itlRef={itlRef} fullWidth />
                </Form.Item>
            </Stack>

            {modalElements}
        </Stack>
    );
};

export default Profile;
