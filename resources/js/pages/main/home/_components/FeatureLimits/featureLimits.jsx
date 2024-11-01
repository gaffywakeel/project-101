import React, {useCallback, useEffect, useMemo, useState} from "react";
import {CardContent, CardHeader, Chip, Stack, Typography} from "@mui/material";
import {errorHandler, route, useRequest} from "@/services/Http";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import FeatureLimit from "@/models/FeatureLimit";
import ResponsiveCard from "@/components/ResponsiveWidgets/responsiveCard";
import Scrollbar from "@/components/Scrollbar";
import {calculatePercent} from "@/utils/helpers";
import CircularProgress from "@/components/CircularProgress";
import LoadingFallback from "@/components/LoadingFallback";
import {useAuth} from "@/models/Auth";

const messages = defineMessages({
    unverified: {defaultMessage: "Unverified"},
    basic: {defaultMessage: "Basic"},
    advanced: {defaultMessage: "Advanced"},
    empty: {defaultMessage: "No Record!"}
});

const FeatureLimits = () => {
    const intl = useIntl();
    const auth = useAuth();
    const [request, loading] = useRequest();
    const [features, setFeatures] = useState([]);
    const {level} = auth.verification;

    const fetchFeatures = useCallback(() => {
        request
            .get(route("feature-limit.all"))
            .then(({data}) => setFeatures(data))
            .catch(errorHandler());
    }, [request]);

    useEffect(() => {
        fetchFeatures();
    }, [fetchFeatures]);

    const tags = useMemo(() => {
        return {
            unverified: (
                <Chip
                    label={intl.formatMessage(messages.unverified)}
                    size="small"
                    variant="outlined"
                />
            ),
            basic: (
                <Chip
                    label={intl.formatMessage(messages.basic)}
                    size="small"
                    color="default"
                />
            ),
            advanced: (
                <Chip
                    label={intl.formatMessage(messages.advanced)}
                    size="small"
                    color="primary"
                />
            )
        };
    }, [intl]);

    return (
        <ResponsiveCard
            sx={{
                display: "flex",
                flexDirection: "column",
                maxHeight: "100%"
            }}>
            <CardHeader
                title={<FormattedMessage defaultMessage="Account Limits" />}
                action={tags[level]}
            />

            <Scrollbar>
                <CardContent>
                    <LoadingFallback
                        content={features}
                        fallbackIconSize={150}
                        loading={loading}>
                        {(features) => (
                            <Stack spacing={3}>
                                {features.map((data) => (
                                    <FeatureItem key={data.name} data={data} />
                                ))}
                            </Stack>
                        )}
                    </LoadingFallback>
                </CardContent>
            </Scrollbar>
        </ResponsiveCard>
    );
};

const FeatureItem = ({data}) => {
    const feature = FeatureLimit.use(data);
    const percent = calculatePercent(feature.usage, feature.limit);

    return (
        <Stack direction="row" alignItems="center" spacing={2}>
            <CircularProgress value={percent} thickness={10} />

            <Stack flexGrow={1} sx={{minWidth: 100}}>
                <Typography variant="body2" noWrap>
                    {feature.title}
                </Typography>

                <Typography variant="caption" color="text.secondary" noWrap>
                    <FormattedMessage
                        defaultMessage="{usage} / {limit} (per {period})"
                        values={{
                            usage: feature.format(feature.usage),
                            limit: feature.format(feature.limit),
                            period: feature.period
                        }}
                    />
                </Typography>
            </Stack>
        </Stack>
    );
};

FeatureLimits.dimensions = {
    lg: {w: 4, h: 3, minW: 4, minH: 3},
    md: {w: 6, h: 3, minW: 6, minH: 3},
    sm: {w: 2, h: 3, minW: 2, minH: 3},
    xs: {w: 1, h: 3, minW: 1, minH: 3}
};

export default FeatureLimits;
