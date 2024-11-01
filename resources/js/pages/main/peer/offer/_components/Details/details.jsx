import React from "react";
import {Card} from "@mui/material";
import UserCard from "./UserCard";
import Reviews from "./Reviews";
import MinAmount from "./MinAmount";
import MaxAmount from "./MaxAmount";
import TimeLimit from "./TimeLimit";
import OfferRate from "./OfferRate";
import Payment from "./Payment";

const Details = () => {
    return (
        <Card>
            <UserCard />
            <Reviews inverse />
            <OfferRate />
            <MaxAmount inverse />
            <MinAmount />
            <Payment inverse />
            <TimeLimit />
        </Card>
    );
};

export default Details;
