import React from "react";
import {Card, Divider} from "@mui/material";
import Type from "../../components/Type";
import Account from "../../components/Account";
import PriceType from "../../components/PriceType";
import Country from "../../components/Country";
import Price from "../../components/Price";
import {SummaryContainer} from "../../components/styled";

const Summary = () => {
    return (
        <Card>
            <SummaryContainer>
                <Type />
                <Account />
                <PriceType />
                <Country />

                <Divider sx={{mb: 1}} />

                <Price />
            </SummaryContainer>
        </Card>
    );
};

export default Summary;
