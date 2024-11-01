import {styled} from "@mui/material/styles";
import {Stack} from "@mui/material";

export const SummaryContainer = styled(Stack)(({theme}) => ({
    "& > .MuiStack-root": {
        padding: theme.spacing(1.5, 2),
        "&:nth-child(even)": {
            backgroundColor: theme.palette.background.neutral
        },
        "&:last-child": {
            backgroundColor: "transparent"
        }
    }
}));
