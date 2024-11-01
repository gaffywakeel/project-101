import React, {useEffect} from "react";
import {useIntl} from "react-intl";
import context, {AppContext} from "@/contexts/AppContext";
import {notify} from "@/utils/index";
import {HelmetProvider} from "react-helmet-async";
import MuiBootstrap from "../MuiBootstrap";
import {getValidationMessages} from "@/utils/form";
import Form from "../Form";

const Bootstrap = ({children}) => {
    const intl = useIntl();

    useEffect(() => {
        const data = context.notification;
        notify[data?.type]?.(data.message);
    }, []);

    return (
        <HelmetProvider>
            <MuiBootstrap>
                <Form.Provider validateMessages={getValidationMessages(intl)}>
                    <AppContext.Provider value={context}>
                        {children}
                    </AppContext.Provider>
                </Form.Provider>
            </MuiBootstrap>
        </HelmetProvider>
    );
};

export default Bootstrap;
