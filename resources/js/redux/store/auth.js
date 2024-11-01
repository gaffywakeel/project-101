import {combineReducers} from "@reduxjs/toolkit";
import authReducer, {initAuthState} from "@/redux/slices/auth";
import globalReducer from "@/redux/slices/global";
import settingsReducer, {initSettingsState} from "@/redux/slices/settings";
import layoutReducer from "@/redux/slices/layout";
import {createStore} from "@/redux/helper";

const reducer = combineReducers({
    auth: authReducer,
    global: globalReducer,
    settings: settingsReducer,
    layout: layoutReducer
});

const auth = createStore(reducer, {
    auth: initAuthState(),
    settings: initSettingsState()
});

export default auth;
