import {createStore} from "@/redux/helper";
import {combineReducers} from "@reduxjs/toolkit";
import settingsReducer, {initSettingsState} from "@/redux/slices/settings";
import authReducer, {initAuthState} from "@/redux/slices/auth";
import globalReducer from "@/redux/slices/global";
import paymentReducer from "@/redux/slices/payment";
import commerceReducer from "@/redux/slices/commerce";
import layoutReducer from "@/redux/slices/layout";
import walletReducer from "@/redux/slices/wallet";

const reducer = combineReducers({
    auth: authReducer,
    global: globalReducer,
    payment: paymentReducer,
    commerce: commerceReducer,
    settings: settingsReducer,
    layout: layoutReducer,
    wallet: walletReducer
});

const index = createStore(reducer, {
    auth: initAuthState(),
    settings: initSettingsState()
});

export default index;
