import React from "react";
import {Card, Stack} from "@mui/material";
import Divider from "@mui/material/Divider";

const ContentCard = (props) => {
    return (
        <Card>
            <Stack
                direction="row"
                divider={
                    <Divider
                        orientation="vertical"
                        sx={{borderStyle: "dashed"}}
                        flexItem={true}
                    />
                }
                sx={{minHeight: 90, p: 2}}
                spacing={2}
                {...props}
            />
        </Card>
    );
};

export default ContentCard;
