import React, {useCallback, useContext} from "react";
import {defineMessages, useIntl} from "react-intl";
import TableContext from "@/contexts/TableContext";
import {route, errorHandler, useRequest} from "@/services/Http";
import {notify} from "@/utils/index";
import {IconButton} from "@mui/material";
import PopConfirm from "@/components/PopConfirm";
import LoadingIcon from "@/components/LoadingIcon";
import CancelIcon from "@mui/icons-material/Cancel";

const messages = defineMessages({
    success: {defaultMessage: "Swap was canceled."}
});

const SwapCancel = ({swap}) => {
    const intl = useIntl();
    const {reload: reloadTable} = useContext(TableContext);
    const [request, loading] = useRequest();

    const cancel = useCallback(() => {
        const url = route("admin.exchange-swaps.cancel", {
            exchange_swap: swap.id
        });

        request
            .post(url)
            .then(() => {
                notify.success(intl.formatMessage(messages.success));
                reloadTable();
            })
            .catch(errorHandler());
    }, [request, swap, intl, reloadTable]);

    if (!swap.cancel_policy) {
        return null;
    }

    return (
        <PopConfirm component={IconButton} onClick={cancel}>
            <LoadingIcon
                color="error"
                component={CancelIcon}
                loading={loading}
            />
        </PopConfirm>
    );
};

export default SwapCancel;
