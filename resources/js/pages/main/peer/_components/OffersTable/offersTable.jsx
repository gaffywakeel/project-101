import React, {useMemo, useRef} from "react";
import PropTypes from "prop-types";
import {Card} from "@mui/material";
import AsyncTable from "@/components/AsyncTable";
import TrapScrollBox from "@/components/TrapScrollBox";
import {route} from "@/services/Http";
import OwnerCell from "@/components/TableCells/PeerOfferTable/OwnerCell";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import PriceCell from "@/components/TableCells/PeerOfferTable/PriceCell";
import LimitCell from "@/components/TableCells/PeerOfferTable/LimitCell";
import PaymentCell from "@/components/TableCells/PeerOfferTable/PaymentCell";
import {assign} from "lodash";
import CoinCell from "@/components/TableCells/CoinCell";
import ActionCell from "@/components/TableCells/PeerOfferTable/ActionCell";

const messages = defineMessages({
    price: {defaultMessage: "Price"},
    coin: {defaultMessage: "Coin"},
    action: {defaultMessage: "Action"},
    limit: {defaultMessage: "Limit"},
    payment: {defaultMessage: "Payment"}
});

const OffersTable = ({type, filters}) => {
    const intl = useIntl();
    const tableRef = useRef();

    const columns = useMemo(
        () => [
            {
                field: "owner",
                minWidth: 200,
                flex: 1,
                renderHeader: () => {
                    if (type === "buy") {
                        return <FormattedMessage defaultMessage="Buyer" />;
                    } else {
                        return <FormattedMessage defaultMessage="Seller" />;
                    }
                },
                renderCell: ({value}) => <OwnerCell user={value} />
            },
            {
                field: "coin",
                width: 70,
                headerName: intl.formatMessage(messages.coin),
                align: "center",
                renderCell: ({value}) => <CoinCell value={value} />
            },
            {
                field: "price",
                width: 150,
                headerName: intl.formatMessage(messages.price),
                renderCell: ({row: data}) => <PriceCell offer={data} />
            },
            {
                field: "limit",
                width: 130,
                headerName: intl.formatMessage(messages.limit),
                renderCell: ({row: data}) => <LimitCell offer={data} />
            },
            {
                field: "payment",
                minWidth: 130,
                flex: 1,
                headerName: intl.formatMessage(messages.payment),
                renderCell: ({row: data}) => <PaymentCell offer={data} />
            },
            {
                field: "action",
                minWidth: 100,
                flex: 0.5,
                headerName: intl.formatMessage(messages.action),
                align: "right",
                renderCell: ({row: data}) => <ActionCell offer={data} />
            }
        ],
        [type, intl]
    );

    const url = useMemo(() => {
        return route("peer-offer.paginate", assign({type}, filters));
    }, [type, filters]);

    return (
        <Card>
            <TrapScrollBox>
                <AsyncTable ref={tableRef} columns={columns} url={url} />
            </TrapScrollBox>
        </Card>
    );
};

OffersTable.propTypes = {
    type: PropTypes.oneOf(["buy", "sell"]),
    filters: PropTypes.object
};

export default OffersTable;
