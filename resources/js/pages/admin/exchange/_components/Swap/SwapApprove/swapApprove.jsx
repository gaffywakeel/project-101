import React, {useCallback, useContext} from "react";
import {notify} from "@/utils/index";
import {defineMessages, useIntl} from "react-intl";
import {route, errorHandler, useRequest} from "@/services/Http";
import TableContext from "@/contexts/TableContext";
import PopConfirm from "@/components/PopConfirm";
import {IconButton} from "@mui/material";
import LoadingIcon from "@/components/LoadingIcon";
import CheckCircleIcon from "@mui/icons-material/CheckCircle";

const messages = defineMessages({
    success: {defaultMessage: "Swap was approved."}
});

const SwapApprove = ({swap}) => {
    const intl = useIntl();
    const {reload: reloadTable} = useContext(TableContext);
    const [request, loading] = useRequest();

    const approve = useCallback(() => {
        const url = route("admin.exchange-swaps.approve", {
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

    if (!swap.approve_policy) {
        return null;
    }

    return (
        <PopConfirm component={IconButton} onClick={approve}>
            <LoadingIcon
                color="success"
                component={CheckCircleIcon}
                loading={loading}
            />
        </PopConfirm>
    );
};

export default SwapApprove;
