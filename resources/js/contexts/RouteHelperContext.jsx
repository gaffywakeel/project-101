import React, {createContext} from "react";
import PropTypes from "prop-types";

const RouteHelperContext = createContext({});

const RouteHelperProvider = ({instance, children}) => {
    return (
        <RouteHelperContext.Provider value={instance}>
            {children}
        </RouteHelperContext.Provider>
    );
};

RouteHelperProvider.propTypes = {
    instance: PropTypes.object.isRequired
};

export {RouteHelperProvider};
export default RouteHelperContext;
