import React, {useContext, useMemo} from "react";
import Collapsible from "../Collapsible";
import {FormattedMessage} from "react-intl";
import {Stack} from "@mui/material";
import PeerTradeContext from "@/contexts/PeerTradeContext";
import PaymentMethod from "./PaymentMethod";
import BankAccount from "./BankAccount";
import ActionButton from "./ActionButton";

const Confirmation = () => {
    const {trade} = useContext(PeerTradeContext);

    const isBankAccount = useMemo(() => {
        return trade.payment === "bank_account";
    }, [trade]);

    return (
        <Collapsible
            title={<FormattedMessage defaultMessage="Confirmation" />}
            content={
                <Stack spacing={3}>
                    {isBankAccount ? <BankAccount /> : <PaymentMethod />}
                    <ActionButton />
                </Stack>
            }
        />
    );
};

export default Confirmation;
