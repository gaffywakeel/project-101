import React, {useCallback, useContext, useEffect, useState} from "react";
import {errorHandler, route, useRequest} from "@/services/Http";
import {Box, Grow} from "@mui/material";
import TransactionCreate from "./TransactionCreate";
import TransactionView from "./TransactionView";
import {isEmpty} from "lodash";
import Spin from "@/components/Spin";
import {useBroadcast} from "@/services/Broadcast";
import {CommerceTransactionProvider} from "@/contexts/CommerceTransactionContext";
import CommerceCustomerContext from "@/contexts/CommerceCustomerContext";
import CommercePaymentContext from "@/contexts/CommercePaymentContext";

const Transaction = () => {
    const [transaction, setTransaction] = useState();
    const {customer} = useContext(CommerceCustomerContext);
    const {payment} = useContext(CommercePaymentContext);
    const [request, loading] = useRequest();

    const transactionBroadcast = useBroadcast(
        `Public.CommerceTransaction.${transaction?.id}`
    );

    const addressBroadcast = useBroadcast(
        `Public.WalletAddress.${transaction?.address}`
    );

    const fetchActiveTransaction = useCallback(() => {
        const url = route("page.commerce-payment.get-active-transaction", {
            payment: payment.id,
            email: customer.email
        });

        request
            .get(url)
            .then(({data}) => setTransaction(data))
            .catch(() => null);
    }, [request, payment, customer]);

    const fetchTransaction = useCallback(() => {
        const url = route("page.commerce-payment.get-transaction", {
            payment: payment.id,
            id: transaction.id
        });

        request
            .get(url)
            .then(({data}) => setTransaction(data))
            .catch(errorHandler());
    }, [request, payment, transaction]);

    useEffect(() => {
        fetchActiveTransaction();
    }, [fetchActiveTransaction]);

    useEffect(() => {
        const channel = ".CommerceTransactionUpdated";
        const handler = (e) => fetchTransaction();

        transactionBroadcast.listen(channel, handler);

        return () => transactionBroadcast.stopListening(channel, handler);
    }, [transactionBroadcast, fetchTransaction]);

    useEffect(() => {
        const channel = "TransferRecordSaved";
        const handler = (e) => fetchTransaction();

        addressBroadcast.listen(channel, handler);

        return () => addressBroadcast.stopListening(channel, handler);
    }, [addressBroadcast, fetchTransaction]);

    const transactionExists = !isEmpty(transaction);

    const onExit = (node) => {
        node.style.position = "absolute";
    };

    return (
        <Box
            sx={{
                margin: "0 auto",
                maxWidth: {xs: "100%", md: 450},
                textAlign: "left"
            }}>
            <CommerceTransactionProvider
                fetchTransaction={fetchTransaction}
                transaction={transaction}>
                <Spin sx={{position: "relative"}} spinning={loading}>
                    <Grow onExit={onExit} in={!transactionExists} unmountOnExit>
                        <TransactionCreate setTransaction={setTransaction} />
                    </Grow>

                    <Grow onExit={onExit} in={transactionExists} unmountOnExit>
                        <TransactionView />
                    </Grow>
                </Spin>
            </CommerceTransactionProvider>
        </Box>
    );
};

export default Transaction;
