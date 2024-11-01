import React, {Fragment, useContext, useEffect, useRef, useState} from "react";
import {defaultTo, isNull, isUndefined} from "lodash";
import intlTelInput from "intl-tel-input";
import "intl-tel-input/build/js/utils";
import {errorHandler, route, useRequest} from "@/services/Http";
import {TextField} from "@mui/material";
import GlobalStyles from "@mui/material/GlobalStyles";
import {FormInputContext} from "../Form/contexts";

const Input = ({itlRef, value, ...others}) => {
    const [request] = useRequest();
    const [info, setInfo] = useState();
    const inputRef = useRef();

    const {
        isRequired,
        label,
        validateStatus,
        errors = []
    } = useContext(FormInputContext);

    function fixControlledValue(value) {
        return isUndefined(value) || isNull(value) ? "" : value;
    }

    const baseProps = {...others};

    switch (validateStatus) {
        case "error":
            baseProps.error = true;
            break;
        case "success":
            baseProps.color = "success";
            break;
        default:
            baseProps.color = "info";
    }

    useEffect(() => {
        request
            .post(route("ip.info"))
            .then(({data}) => setInfo(data))
            .catch(errorHandler());
    }, [request]);

    useEffect(() => {
        if (inputRef.current) {
            const phoneInput = intlTelInput(inputRef.current, {
                nationalMode: false,
                initialCountry: defaultTo(info?.iso_code, "")
            });

            itlRef.current = phoneInput;

            return () => phoneInput.destroy();
        }
    }, [itlRef, info]);

    return (
        <Fragment>
            <TextField
                {...baseProps}
                required={isRequired}
                value={fixControlledValue(value)}
                label={label}
                InputLabelProps={{shrink: true}}
                helperText={errors}
                inputRef={inputRef}
            />

            {globalStyle}
        </Fragment>
    );
};

const globalStyle = (
    <GlobalStyles
        styles={(theme) => ({
            ".iti__country-list": {
                borderRadius: theme.shape.borderRadius,
                backgroundColor: theme.palette.background.paper,
                boxShadow: theme.customShadows.z8
            }
        })}
    />
);

export default Input;
