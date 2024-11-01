import React, {useCallback} from "react";
import {Card, CardContent, InputAdornment, Stack} from "@mui/material";
import Form from "@/components/Form";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {useDispatch} from "react-redux";
import {LoadingButton} from "@mui/lab";
import useModal from "@/hooks/useModal";
import {fetchWalletAccounts} from "@/redux/slices/wallet";
import {errorHandler, route, useFormRequest} from "@/services/Http";
import {useWalletAccounts} from "@/hooks/accounts";
import {notify} from "@/utils/index";
import InputValue from "./InputValue";
import InputAccount from "./InputAccount";
import InputDivider from "./InputDivider";
import Alerts from "./Alerts";
import Confirmation from "./Confirmation";
import useFormRules from "@/hooks/useFormRules";
import useFormReset from "@/hooks/useFormReset";
import {isEmpty} from "lodash";
import {normalizeNumber} from "@/utils/form";

const messages = defineMessages({
    from: {defaultMessage: "From"},
    to: {defaultMessage: "To"},
    confirm: {defaultMessage: "Confirm"},
    success: {defaultMessage: "Swap was created."}
});

const Action = ({closeModal}) => {
    const intl = useIntl();
    const dispatch = useDispatch();
    const [form] = Form.useForm();
    const [modal, modalElements] = useModal();
    const [formRequest, formLoading] = useFormRequest(form);
    const {accounts} = useWalletAccounts();
    const rules = useFormRules();

    useFormReset(form, !isEmpty(accounts));

    const submitForm = useCallback(
        (values) => {
            formRequest
                .post(route("exchange-swaps.store"), values)
                .then(() => {
                    notify.success(intl.formatMessage(messages.success));
                    dispatch(fetchWalletAccounts());
                    closeModal();
                })
                .catch(errorHandler());
        },
        [formRequest, dispatch, intl, closeModal]
    );

    const handleValuesChange = useCallback(
        (changes, values) => {
            if (changes.buy_value && values.sell_value) {
                form.setFieldsValue({sell_value: undefined});
            }

            if (changes.sell_value && values.buy_value) {
                form.setFieldsValue({buy_value: undefined});
            }

            if (values.sell_account === values.buy_account) {
                if (changes.sell_account) {
                    form.setFieldsValue({sell_account: undefined});
                }

                if (changes.buy_account) {
                    form.setFieldsValue({buy_account: undefined});
                }
            }
        },
        [form]
    );

    const confirm = useCallback(() => {
        form.validateFields([
            "buy_account",
            "buy_value",
            "sell_account",
            "sell_value"
        ])
            .then((values) => {
                formRequest
                    .post(route("exchange-swap.calculate"), values)
                    .then(({data: summary}) => {
                        dispatch(fetchWalletAccounts());

                        modal.confirm({
                            key: "confirmation",
                            title: intl.formatMessage(messages.confirm),
                            content: <Confirmation summary={summary} />,
                            dialogProps: {fullWidth: true, maxWidth: "sm"}
                        });
                    })
                    .catch(errorHandler());
            })
            .catch(console.debug);
    }, [form, formRequest, modal, intl, dispatch]);

    return (
        <Card>
            <CardContent>
                <Form
                    form={form}
                    onValuesChange={handleValuesChange}
                    onFinish={submitForm}>
                    <Stack spacing={3} sx={{py: 2}}>
                        <Alerts />

                        <Form.Item
                            name="sell_value"
                            label={intl.formatMessage(messages.from)}
                            dependencies={["buy_value"]}
                            normalize={normalizeNumber}
                            rules={[
                                rules.requiredWithout("buy_value"),
                                {type: "number"}
                            ]}>
                            <InputValue
                                accountName="sell_account"
                                InputProps={{
                                    endAdornment: (
                                        <InputAdornment position="end">
                                            <Form.Item
                                                name="sell_account"
                                                initialValue={accounts[0]?.id}
                                                rules={[{required: true}]}>
                                                <InputAccount dependency="buy_account" />
                                            </Form.Item>
                                        </InputAdornment>
                                    )
                                }}
                            />
                        </Form.Item>

                        <InputDivider />

                        <Form.Item
                            name="buy_value"
                            label={intl.formatMessage(messages.to)}
                            dependencies={["sell_value"]}
                            normalize={normalizeNumber}
                            rules={[
                                rules.requiredWithout("sell_value"),
                                {type: "number"}
                            ]}>
                            <InputValue
                                accountName="buy_account"
                                InputProps={{
                                    endAdornment: (
                                        <InputAdornment position="end">
                                            <Form.Item
                                                name="buy_account"
                                                initialValue={accounts[1]?.id}
                                                rules={[{required: true}]}>
                                                <InputAccount dependency="sell_account" />
                                            </Form.Item>
                                        </InputAdornment>
                                    )
                                }}
                            />
                        </Form.Item>

                        {modalElements}

                        <LoadingButton
                            variant="contained"
                            loading={formLoading}
                            onClick={confirm}
                            fullWidth>
                            <FormattedMessage defaultMessage="Proceed" />
                        </LoadingButton>
                    </Stack>
                </Form>
            </CardContent>
        </Card>
    );
};

export default Action;
