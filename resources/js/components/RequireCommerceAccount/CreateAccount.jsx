import React from "react";
import {FormattedMessage} from "react-intl";
import {Button, Container} from "@mui/material";
import Result from "../Result";
import Spin from "../Spin";
import {useCommerceAccount} from "@/hooks/accounts";
import {styled} from "@mui/material/styles";
import Page from "@/components/Page";
import {Link} from "react-router-dom";

const CreateAccount = () => {
    const {loading} = useCommerceAccount();

    return (
        <StyledPage>
            <Container>
                <Spin spinning={loading}>
                    <Result
                        title={
                            <FormattedMessage defaultMessage="No account yet." />
                        }
                        description={
                            <FormattedMessage defaultMessage="You have no active commerce account." />
                        }
                        extra={
                            <Button
                                variant="contained"
                                to="/main/commerce/account"
                                component={Link}>
                                <FormattedMessage defaultMessage="Create Account" />
                            </Button>
                        }
                        iconSize={200}
                    />
                </Spin>
            </Container>
        </StyledPage>
    );
};

const StyledPage = styled(Page)(({theme}) => ({
    display: "flex",
    alignItems: "center",
    paddingTop: theme.spacing(10),
    paddingBottom: theme.spacing(10),
    minHeight: "100%"
}));

export default CreateAccount;
