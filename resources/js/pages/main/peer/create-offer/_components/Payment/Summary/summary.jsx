import React from "react";
import {Card, Divider} from "@mui/material";
import Type from "../../components/Type";
import Account from "../../components/Account";
import Price from "../../components/Price";
import PriceType from "../../components/PriceType";
import MinAmount from "../../components/MinAmount";
import MaxAmount from "../../components/MaxAmount";
import Payment from "../../components/Payment";
import TimeLimit from "../../components/TimeLimit";
import {SummaryContainer} from "../../components/styled";

const Summary = () => {
    return (
        <Card>
            <SummaryContainer>
                <Type />
                <Account />
                <PriceType />
                <Payment />
                <TimeLimit />
                <MinAmount />
                <MaxAmount />

                <Divider sx={{mb: 1}} />

                <Price />
            </SummaryContainer>
        </Card>
    );
};

export default Summary;
