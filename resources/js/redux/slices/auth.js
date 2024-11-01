import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {route, thunkRequest} from "@/services/Http";
import context from "@/contexts/AppContext";
import {assign} from "lodash";

const authState = {
    user: null,
    credential: "email",
    setup: false,
    permissions: [],
    verification: {
        loading: false,
        error: null,
        level: "unverified",
        basic: [],
        advanced: []
    }
};

export const initAuthState = () => {
    return assign({}, authState, context.auth);
};

export const fetchUser = createAsyncThunk("auth/fetchUser", (arg, api) => {
    return thunkRequest(api).get(route("user.get"));
});

export const fetchVerification = createAsyncThunk(
    "auth/fetchVerification",
    (arg, api) => {
        return thunkRequest(api).get(route("user.verification.get"));
    }
);

const auth = createSlice({
    name: "auth",
    initialState: authState,
    extraReducers: {
        [fetchUser.fulfilled]: (state, action) => {
            state.user = action.payload;
        },
        [fetchVerification.pending]: (state) => {
            state.verification = {
                ...state.verification,
                error: null,
                loading: true
            };
        },
        [fetchVerification.fulfilled]: (state, action) => {
            state.verification = {
                ...state.verification,
                loading: false,
                error: null,
                basic: action.payload.basic,
                advanced: action.payload.advanced,
                level: action.payload.level
            };
        },
        [fetchVerification.rejected]: (state, action) => {
            state.verification = {
                ...state.verification,
                error: action.error.message,
                loading: false
            };
        }
    }
});

export default auth.reducer;
