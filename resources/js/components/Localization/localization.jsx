import React, {useEffect} from "react";
import {useDispatch, useSelector} from "react-redux";
import {fetchLocale} from "@/redux/slices/settings";
import {get} from "lodash";
import {IntlProvider} from "react-intl";

const Localization = ({children}) => {
    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(fetchLocale());
    }, [dispatch]);

    const {messages, locale} = useSelector((state) => {
        return get(state, "settings.locale.data");
    });

    return (
        <IntlProvider locale={locale} messages={messages}>
            {children}
        </IntlProvider>
    );
};

export default Localization;
