import React, {useCallback} from "react";
import {useDemo} from "@/hooks/settings";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {LoadingButton} from "@mui/lab";
import {errorHandler, route, useRequest} from "@/services/Http";
import {notify} from "@/utils/index";

const messages = defineMessages({
    success: {defaultMessage: "Login was successful."}
});

const DemoLogin = () => {
    const demo = useDemo();
    const intl = useIntl();
    const [request, loading] = useRequest();

    const login = useCallback(() => {
        request
            .post(route("auth.demo-login"))
            .then(() => {
                notify.success(intl.formatMessage(messages.success));
                window.location.reload();
            })
            .catch(errorHandler());
    }, [request, intl]);

    if (!demo) {
        return null;
    }

    return (
        <LoadingButton
            size="large"
            variant="outlined"
            color="inherit"
            onClick={login}
            loading={loading}
            fullWidth>
            <FormattedMessage defaultMessage="Login as Demo" />
        </LoadingButton>
    );
};

export default DemoLogin;
