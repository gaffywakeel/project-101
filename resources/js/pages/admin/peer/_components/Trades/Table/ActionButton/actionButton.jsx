import React, {useCallback, useMemo} from "react";
import ActionCell from "@/components/TableCells/PeerTradeTable/ActionCell";
import {LoadingButton} from "@mui/lab";
import {FormattedMessage} from "react-intl";
import {errorHandler, route, useRequest} from "@/services/Http";
import {useNavigate} from "react-router-dom";

const ActionButton = ({trade}) => {
    const [request, loading] = useRequest();
    const navigate = useNavigate();

    const showAction = useMemo(() => {
        return trade.status === "disputed" && !trade.role;
    }, [trade]);

    const joinTrade = useCallback(() => {
        request
            .post(route("admin.peer-trade.join", {trade: trade.id}))
            .then(() => navigate(`/main/peer/trades/${trade.id}`))
            .catch(errorHandler());
    }, [request, trade, navigate]);

    if (!showAction) {
        return null;
    }

    if (trade.is_participant) {
        return <ActionCell trade={trade} />;
    }

    return (
        <LoadingButton
            color="primary"
            onClick={joinTrade}
            variant="contained"
            loading={loading}>
            <FormattedMessage defaultMessage="Join" />
        </LoadingButton>
    );
};

export default ActionButton;
