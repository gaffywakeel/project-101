import React from "react";
import {Icon} from "@iconify/react";
import {styled} from "@mui/material/styles";
import {Alert, Box, Chip, Stack} from "@mui/material";
import {FormattedMessage} from "react-intl";
import checkboxCircleFill from "@iconify-icons/ri/checkbox-circle-fill";
import closeCircleFill from "@iconify-icons/ri/close-circle-fill";
import {isEmpty} from "lodash";

const Basic = ({data, ...props}) => {
    return (
        <Box {...props}>
            <Alert severity="warning">
                <FormattedMessage defaultMessage="This stage requires some of your account information to be completed, the validity will be checked during advanced verification." />
            </Alert>

            {!isEmpty(data) && (
                <Stack justifyContent="center" alignItems="center">
                    <Box sx={{display: "inline-block"}}>
                        <Stack spacing={1} alignItems="center" sx={{p: 3}}>
                            {data.map((item, key) => (
                                <VerificationItem item={item} key={key} />
                            ))}
                        </Stack>
                    </Box>
                </Stack>
            )}
        </Box>
    );
};

const VerificationItem = ({item}) => {
    const icon = !item.status ? (
        <StyledIcon icon={closeCircleFill} color="error" />
    ) : (
        <StyledIcon icon={checkboxCircleFill} color="success" />
    );

    return <Chip label={item.title} icon={icon} />;
};

const StyledIcon = styled(({color, ...props}) => {
    return <Icon {...props} />;
})(({theme, color}) => ({
    color: `${theme.palette[color].main} !important`,
    width: 20,
    height: 20
}));

export default Basic;
