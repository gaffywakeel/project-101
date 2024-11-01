import React, {useCallback, useContext} from "react";
import SwapCircleIcon from "@mui/icons-material/SwapVerticalCircle";
import {IconButton, Stack} from "@mui/material";
import {FormContext} from "@/components/Form/contexts";

const InputDivider = () => {
    const {form} = useContext(FormContext);

    const switchAccount = useCallback(() => {
        form.setFieldsValue({
            sell_account: form.getFieldValue("buy_account"),
            sell_value: form.getFieldValue("buy_value"),
            buy_account: form.getFieldValue("sell_account"),
            buy_value: form.getFieldValue("sell_value")
        });
    }, [form]);

    return (
        <Stack direction="row" justifyContent="center" alignItems="center">
            <IconButton onClick={switchAccount}>
                <SwapCircleIcon fontSize="large" />
            </IconButton>
        </Stack>
    );
};

export default InputDivider;
