import React, {useCallback, useState} from "react";
import {Button, Card, Stack} from "@mui/material";
import StakePlan from "@/models/StakePlan";
import PlanTitle from "./PlanTitle";
import PlanRates from "./PlanRates";
import {first} from "lodash";
import PlanInfo from "./PlanInfo";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import useModal from "@/hooks/useModal";
import StakeForm from "./StakeForm";

const messages = defineMessages({
    stake: {defaultMessage: "Stake {symbol}"}
});

const PlanItem = ({item}) => {
    const plan = StakePlan.use(item);
    const intl = useIntl();
    const [activeRate, setActiveRate] = useState(() => first(plan.rates));
    const [modal, modalElements] = useModal();

    const stake = useCallback(() => {
        const symbol = plan.wallet.coin.symbol;

        modal.confirm({
            title: intl.formatMessage(messages.stake, {symbol}),
            content: <StakeForm plan={plan} rate={activeRate} />,
            dialogProps: {fullWidth: true}
        });
    }, [modal, intl, plan, activeRate]);

    return (
        <Card className="animated slideInUp">
            <Stack sx={{p: 3}} spacing={3}>
                <PlanTitle plan={plan} />

                <PlanRates
                    rates={plan.rates}
                    onChange={setActiveRate}
                    value={activeRate}
                />

                <PlanInfo plan={plan} rate={activeRate} />

                {modalElements}

                <Button variant="contained" onClick={stake} fullWidth>
                    <FormattedMessage defaultMessage="Stake Now" />
                </Button>
            </Stack>
        </Card>
    );
};

export default PlanItem;
