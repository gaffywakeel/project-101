import React, {useCallback} from "react";
import {Card, CardContent, CardHeader} from "@mui/material";
import {FormattedMessage} from "react-intl";
import {LoadingButton} from "@mui/lab";
import {TwoFactorIllustration} from "@/assets/index";
import Result from "@/components/Result";
import {useAuth} from "@/models/Auth";
import useModal from "@/hooks/useModal";
import AddDevice from "./AddDevice";

const TwoFactor = () => {
    const auth = useAuth();
    const [modal, modalElements] = useModal();

    const addDevice = useCallback(() => {
        modal.confirm({
            content: <AddDevice />
        });
    }, [modal]);

    return (
        <Card>
            <CardHeader
                title={<FormattedMessage defaultMessage="2FA Security" />}
            />

            <CardContent>
                {modalElements}

                {auth.user.enabledTwoFactor() ? (
                    <Result
                        title={
                            <FormattedMessage defaultMessage="2FA Enabled." />
                        }
                        description={
                            <FormattedMessage defaultMessage="You have enabled two factor." />
                        }
                        icon={TwoFactorIllustration}
                        iconSize={130}
                        extra={
                            <LoadingButton
                                variant="contained"
                                onClick={addDevice}>
                                <FormattedMessage defaultMessage="Add Device" />
                            </LoadingButton>
                        }
                    />
                ) : (
                    <Result
                        title={
                            <FormattedMessage defaultMessage="2FA Disabled." />
                        }
                        description={
                            <FormattedMessage defaultMessage="We highly recommend securing your account with 2FA." />
                        }
                        iconSize={130}
                        extra={
                            <LoadingButton
                                variant="contained"
                                onClick={addDevice}>
                                <FormattedMessage defaultMessage="Add Device" />
                            </LoadingButton>
                        }
                    />
                )}
            </CardContent>
        </Card>
    );
};

export default TwoFactor;
