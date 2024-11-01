import React from "react";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import Form, {TextField} from "@/components/Form";
import {InputAdornment} from "@mui/material";

const messages = defineMessages({
    fixedPrice: {defaultMessage: "Fixed Price"},
    percentPrice: {defaultMessage: "Price Margin"}
});

const PriceField = () => {
    const intl = useIntl();
    const currency = Form.useWatch("currency");
    const priceType = Form.useWatch("price_type");

    return priceType === "percent" ? (
        <Form.Item
            name="percent_price"
            label={intl.formatMessage(messages.percentPrice)}
            rules={[{required: true, type: "number", min: 1}]}
            normalize={(v) => v && parseFloat(v)}
            initialValue={100}>
            <TextField
                type="number"
                InputLabelProps={{shrink: true}}
                fullWidth
                InputProps={{
                    endAdornment: (
                        <InputAdornment position="end">
                            <FormattedMessage defaultMessage="%" />
                        </InputAdornment>
                    )
                }}
            />
        </Form.Item>
    ) : (
        <Form.Item
            name="fixed_price"
            normalize={(v) => v && parseFloat(v)}
            label={intl.formatMessage(messages.fixedPrice)}
            rules={[{required: true, type: "number"}]}>
            <TextField
                type="number"
                InputLabelProps={{shrink: true}}
                fullWidth
                InputProps={{
                    endAdornment: (
                        <InputAdornment position="end">
                            {currency}
                        </InputAdornment>
                    )
                }}
            />
        </Form.Item>
    );
};

export default PriceField;
