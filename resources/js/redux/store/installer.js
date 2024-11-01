import {combineReducers} from "@reduxjs/toolkit";
import authReducer, {initAuthState} from "../slices/auth";
import settingsReducer, {initSettingsState} from "../slices/settings";
import {createStore} from "../helper";

const reducer = combineReducers({
    auth: authReducer,
    settings: settingsReducer
});

const installer = createStore(reducer, {
    auth: initAuthState(),
    settings: initSettingsState()
});

export default installer;
